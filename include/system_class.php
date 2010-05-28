<?php
//-- データベース処理の規定クラス --//
class DatabaseConfigBase{
  //データベース接続
  /*
    $header : HTMLヘッダ出力情報 [true: 出力済み / false: 未出力]
    $exit   : エラー処理 [true: exit を返す / false で終了]
  */
  function Connect($header = false, $exit = true){
    //データベースサーバにアクセス
    $db_handle = mysql_connect($this->host, $this->user, $this->password);
    if($db_handle){ //アクセス成功
      mysql_set_charset('ujis');
      if(mysql_select_db($this->name, $db_handle)){ //データベース接続
	//mysql_query("SET NAMES utf8");
	//成功したらハンドルを返して処理終了
	$this->db_handle = $db_handle;
	return $db_handle;
      }
      else{
	$error_title = 'データベース接続失敗';
	$error_name  = $this->name;
      }
    }
    else{
      $error_title = 'MySQLサーバ接続失敗';
      $error_name  = $this->host;
    }

    $error_message = $error_title . ': ' . $error_name; //エラーメッセージ作成
    if($header){
      echo '<font color="#FF0000">' . $error_message . '</font><br>';
      if($exit) OutputHTMLFooter($exit);
      return false;
    }
    OutputActionResult($error_title, $error_message);
  }

  //データベースとの接続を閉じる
  function Disconnect($unlock = false){
    if(is_null($this->db_handle)) return;

    if($unlock) UnlockTable(); //ロック解除
    mysql_close($this->db_handle);
    unset($this->db_handle); //ハンドルをクリア
  }
}

//-- セッション管理クラス --//
class Session{
  var $id;
  var $user_no;

  function Session(){ $this->__construct(); }
  function __construct(){
    session_start();
    $this->Set();
  }

  //ID セット
  function Set(){
    $this->id = session_id();
    return $this->id;
  }

  //ID リセット
  function Reset(){
    //PHP のバージョンが古い場合は関数がないので自前で処理する
    if(function_exists('session_regenerate_id')){
      session_regenerate_id();
    }
    else{
      $id = serialize($_SESSION);
      session_destroy();
      session_id(md5(uniqid(rand(), 1)));
      session_start();
      $_SESSION = unserialize($id);
    }
    return $this->Set();
  }

  //ID 取得
  function Get($uniq = false){
    return $uniq ? $this->GetUniq() : $this->id;
  }

  //DB に登録されているセッション ID と被らないようにする
  function GetUniq(){
    $query = 'SELECT COUNT(room_no) FROM user_entry WHERE session_id = ';
    do{
      $this->Reset();
    }while(FetchResult($query ."'{$this->id}'") > 0);
    return $this->id;
  }

  function GetUser(){
    return $this->user_no;
  }

  //認証
  function Certify($exit = true){
    global $RQ_ARGS;
    //$ip_address = $_SERVER['REMOTE_ADDR']; //IPアドレス認証は現在は行っていない

    //セッション ID による認証
    $query = "SELECT user_no FROM user_entry WHERE room_no = {$RQ_ARGS->room_no} " .
      "AND session_id ='{$this->id}' AND user_no > 0";
    $array = FetchArray($query);
    if(count($array) == 1){
      $this->user_no = $array[0];
      return true;
    }

    if($exit){ //エラー処理
      $title = 'セッション認証エラー';
      $sentence = $title . "\n" . '<a href="./" target="_top">トップページ</a>から' .
	'ログインしなおしてください';
      OutputActionResult($title, $sentence);
    }
    return false;
  }
}

//-- クッキーデータのロード処理 --//
class CookieDataSet{
  var $day_night;  //夜明け
  var $vote_times; //投票回数
  var $objection;  //「異議あり」の情報

  function CookieDataSet(){ $this->__construct(); }
  function __construct(){
    $this->day_night  = $_COOKIE['day_night'];
    $this->vote_times = (int)$_COOKIE['vote_times'];
    $this->objection  = $_COOKIE['objection'];
  }
}

//-- 外部リンク生成クラス --//
class ExternalLinkBuilder{
  //サーバ通信状態チェック
  function CheckConnection($url){
    $url_stack = explode('/', $url);
    $this->host = $url_stack[2];
    $io = @fsockopen($this->host, 80, $err_no, $err_str, 3);
    if(! $io) return false;

    stream_set_timeout($io, 3);
    fwrite($io, "GET / HTTP/1.1\r\nHost: {$host}\r\nConnection: Close\r\n\r\n");
    $data = fgets($io, 128);
    $stream_stack = stream_get_meta_data($io);
    fclose($io);
    //PrintData($data, 'Connection');
    return ! $stream_stack['timed_out'];
  }

  function Generate($title, $data){
    return <<<EOF
<fieldset>
<legend>{$title}</legend>
<div class="game-list"><dl>{$data}</dl></div>
</fieldset>

EOF;
  }

  function GenerateBBS($data){
    $title = '<a href="' . $this->view_url . $this->thread . 'l50' . '">告知スレッド情報</a>';
    return $this->Generate($title, $data);
  }

  function GenerateSharedServerRoom($name, $url, $data){
    return $this->Generate('ゲーム一覧 (<a href="' . $url . '">' . $name . '</a>)', $data);
  }
}

//-- 掲示板情報取得の基底クラス --//
class BBSConfigBase extends ExternalLinkBuilder{
  function Output(){
    global $SERVER_CONF;

    if($this->disable) return;
    if(! $this->CheckConnection($this->raw_url)){
      echo $this->GenerateBBS($this->host . ': Connection timed out (3 seconds)');
      return;
    }

    //スレッド情報を取得
    $url = $this->raw_url . $this->thread . 'l' . $this->size . 'n';
    if(($data = @file_get_contents($url)) == '') return;
    //PrintData($data, 'Data'); //テスト用
    if($this->encode != $SERVER_CONF->encode){
      $data = mb_convert_encoding($data, $SERVER_CONF->encode, $this->encode);
    }
    $str = '';
    $str_stack = explode("\n", $data);
    array_pop($str_stack);
    foreach($str_stack as $res){
      $res_stack = explode('<>', $res);
      $str .= '<dt>' . $res_stack[0] . ' : <font color="#008800"><b>' . $res_stack[1] .
	'</b></font> : ' . $res_stack[3] . ' ID : ' . $res_stack[6] . '</dt>' . "\n" .
	'</dt><dd>' . $res_stack[4] . '</dd>';
    }
    echo $this->GenerateBBS($str);
  }
}

//-- ユーザアイコン管理の基底クラス --//
class UserIconBase{
  // アイコンの文字数
  function IconNameMaxLength(){
    return '半角で' . $this->name . '文字、全角で' . floor($this->name / 2) . '文字まで';
  }

  // アイコンのファイルサイズ
  function IconFileSizeMax(){
    return ($this->size > 1024 ? floor($this->size / 1024) . 'k' : $this->size) . 'Byte まで';
  }

  // アイコンの縦横のサイズ
  function IconSizeMax(){
    return '幅' . $this->width . 'ピクセル × 高さ' . $this->height . 'ピクセルまで';
  }
}

//-- 画像管理の基底クラス --//
class ImageManager{
  function Generate($name, $alt = ''){
    $str = '<img';
    if($this->class != '') $str .= ' class="' . $this->class . '"';
    $str .= ' src="' . JINRO_IMG . '/' . $this->path . '/' . $name . '.' . $this->extension . '"';
    if($alt != ''){
      EscapeStrings(&$alt);
      $str .= ' alt="' . $alt . '" title="' . $alt . '"';
    }
    return $str . '>';
  }

  function Output($name){
    echo $this->Generate($name) . "<br>\n";
  }
}

//-- 勝利陣営の画像処理の基底クラス --//
class VictoryImageBase extends ImageManager{
  function Generate($name){
    switch($name){
    case 'human':
      $alt = '村人勝利';
      break;

    case 'wolf':
      $alt = '人狼勝利';
      break;

    case 'fox1':
    case 'fox2':
      $name = 'fox';
      $alt = '妖狐勝利';
      break;

    case 'lovers':
      $alt = '恋人勝利';
      break;

    case 'quiz':
      $alt = '出題者勝利';
      break;

    case 'draw':
    case 'vanish':
    case 'quiz_dead':
      $name = 'draw';
      $alt = '引き分け';
      break;

    default:
      return '-';
      break;
    }
    return parent::Generate($name, $alt);
  }
}

//-- メニューリンク表示用の基底クラス --//
class MenuLinkConfigBase{
  //交流用サイト表示
  function Output(){
    //初期化処理
    $this->str = '';
    $this->header = '<li>';
    $this->footer = "</li>\n";

    $this->AddHeader('交流用サイト');
    $this->AddLink($this->list);
    $this->AddFooter();

    if(count($this->add_list) > 0){
      $this->AddHeader('外部リンク');
      foreach($this->add_list as $group => $list){
	$this->str .= $this->header . $group . $this->footer;
	$this->AddLink($list);
      }
      $this->AddFooter();
    }
    echo $this->str;
  }

  //ヘッダ追加
  function AddHeader($title){
    $this->str .= '<div class="menu">' . $title . "</div>\n<ul>\n";
  }

  //リンク生成
  function AddLink($list){
    $header = $this->header . '<a href="';
    $footer = '</a>' . $this->footer;
    foreach($list as $name => $url) $this->str .= $header . $url . '">' . $name . $footer;
  }

  //フッタ追加
  function AddFooter(){
    $this->str .= "</ul>\n";
  }
}

//-- Copyright 表示用の基底クラス --//
class CopyrightConfigBase{
  //投稿処理
  function Output(){
    $stack = $this->list;
    foreach($this->add_list as $class => $list){
      $stack[$class] = array_key_exists($class, $stack) ? array_merge($stack[$class], $list) :
	$list;
    }

    foreach($stack as $class => $list){
      $str = '<h2>' . $class . '</h2>'."\n";
      foreach($list as $name => $url){
	$str .= '<a href="' . $url . '">' . $name . '</a><br>'."\n";
      }
      echo $str;
    }
  }
}

//-- 音源処理の基底クラス --//
class SoundBase{
  //音を鳴らす
  function Output($type, $loop = false){
    $path = JINRO_ROOT . '/' . $this->path . '/' . $this->$type . '.' . $this->extension;
    if($loop) $loop_tag = "\n".'<param name="loop" value="true">';

    echo <<< EOF
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4,0,0,0" width="0" height="0">
<param name="movie" value="{$path}">
<param name="quality" value="high">{$loop_tag}
<embed src="{$path}" type="application/x-shockwave-flash" quality="high" width="0" height="0" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
</embed>
</object>

EOF;
  }
}

//-- Twitter 投稿用の基底クラス --//
class TwitterConfigBase{
  //投稿処理
  function Send($id, $name, $comment){
    if($this->disable) return;
    require_once(JINRO_MOD . "/twitter/Twitter.php"); //ライブラリをロード

    $message = "【{$this->server}】{$id}番地に{$name}村\n〜{$comment}〜 が建ちました";
    $st =& new Services_Twitter($this->user, $this->password);
    if($st->setUpdate(mb_convert_encoding($message, 'UTF-8', 'auto'))) return;

    //エラー処理
    $sentence = 'Twitter への投稿に失敗しました。<br>'."\n" .
      'ユーザ名：' . $this->user . '<br>'."\n" . 'メッセージ：' . $message;
    PrintData($sentence);
  }
}

//-- ページ送りリンク生成クラス --//
class PageLinkBuilder{
  function PageLinkBuilder($file, $page, $count, $config, $title = 'Page', $type = 'page'){
    $this->__construct($file, $page, $count, $config, $title, $type);
  }
  function __construct($file, $page, $count, $config, $title = 'Page', $type = 'page'){
    $this->view_total = $count;
    $this->view_page  = $config->page;
    $this->view_count = $config->view;
    $this->reverse    = $config->reverse;

    $this->file   = $file;
    $this->url    = '<a href="' . $file . '.php?';
    $this->title  = $title;
    $this->type   = $type;
    $this->option = array();
    $this->SetPage($page);
  }

  //表示するページのアドレスをセット
  function SetPage($page){
    $total = ceil($this->view_total / $this->view_count);
    $start = $page == 'all' ? 1 : $page;
    if($total - $start < $this->view_page){ //残りページが少ない場合は表示開始位置をずらす
      $start = $total - $this->view_page + 1;
      if($start < 1) $start = 1;
    }
    $end = $start + $this->view_page - 1;
    if($end > $total) $end = $total;

    $this->page->set   = $page;
    $this->page->total = $total;
    $this->page->start = $start;
    $this->page->end   = $end;

    $this->limit = $page == 'all' ? '' : $this->view_count * ($page - 1);
    $this->query = $page == 'all' ? '' : sprintf(' LIMIT %d, %d', $this->limit, $this->view_count);
  }

  //オプションを追加する
  function AddOption($type, $value = 'on'){
    $this->option[$type] = $type . '=' . $value;
  }

  //ページ送り用のリンクタグを作成する
  function Generate($page, $title = NULL, $force = false){
    if($page == $this->page->set && ! $force) return '[' . $page . ']';
    $list = $this->option;
    array_unshift($list, $this->type . '=' . $page);
    if(is_null($title)) $title = '[' . $page . ']';
    return $this->url . implode('&', $list) . '">' . $title . '</a>';
  }

  //ページリンクを出力する
  function Output(){
    $url_stack = array('[' . $this->title . ']');
    if($this->page->start > 1 && $this->page->total > $this->view_page){
      $url_stack[] = $this->Generate(1, '[1]...');
      $url_stack[] = $this->Generate($this->page->start - 1, '&lt;&lt;');
    }

    for($i = $this->page->start; $i <= $this->page->end; $i++){
      $url_stack[] = $this->Generate($i);
    }

    if($this->page->end < $this->page->total){
      $url_stack[] = $this->Generate($this->page->end + 1, '&gt;&gt;');
      $url_stack[] = $this->Generate($this->page->total, '...[' . $this->page->total . ']');
    }
    $url_stack[] = $this->Generate('all');

    if($this->file == 'old_log'){
      $this->AddOption('reverse', $this->set_reverse ? 'off' : 'on');
      $url_stack[] = '[表示順]';
      $url_stack[] = $this->set_reverse ? '新↓古' : '古↓新';
      $name = ($this->set_reverse xor $this->reverse) ? '元に戻す' : '入れ替える';
      $url_stack[] =  $this->Generate($this->page->set, $name, true);
    }
    echo $this->header . implode(' ', $url_stack) . $this->footer;
  }
}

//-- 配役設定の基底クラス --//
class CastConfigBase{
  //「福引き」を一定回数行ってリストに追加する
  function AddRandom(&$list, $random_list, $count){
    $total = count($random_list) - 1;
    for(; $count > 0; $count--) $list[$random_list[mt_rand(0, $total)]]++;
  }

  //「比」の配列から「福引き」を作成する
  function GenerateRandomList($list){
    $stack = array();
    foreach($list as $role => $rate){
      for($i = $rate; $i > 0; $i--) $stack[] = $role;
    }
    return $stack;
  }

  //「比」から「確率」に変換する (テスト用)
  function RateToProbability($list){
    $stack = array();
    $total_rate = array_sum($list);
    foreach($list as $role => $rate){
      $stack[$role] = sprintf("%01.2f", $rate / $total_rate * 100);
    }
    PrintData($stack);
  }
}
