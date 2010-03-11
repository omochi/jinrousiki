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

    if($unlock) mysql_query('UNLOCK TABLES'); //ロック解除
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
    $query = 'SELECT COUNT(room_no) FROM user_entry, admin_manage WHERE ' .
      'user_entry.session_id =';
    do{
      $this->Reset();
    }while(FetchResult($query ."'{$this->id}' OR admin_manage.session_id = '{$this->id}'") > 0);
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
      OutputActionResult('セッション認証エラー',
			 'セッション認証エラー<br>'."\n" .
			 '<a href="./" target="_top">トップページ</a>から' .
			 'ログインしなおしてください');
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
