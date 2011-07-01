<?php
//-- データベース処理の基底クラス --//
class DatabaseConfigBase{
  //データベース接続
  /*
    $header : HTML ヘッダ出力情報 [true: 出力済み / false: 未出力]
    $exit   : エラー処理 [true: exit を返す / false で終了]
  */
  function Connect($header = false, $exit = true){
    //データベースサーバにアクセス
    if(! ($db_handle = mysql_connect($this->host, $this->user, $this->password))){
      return $this->OutputError($header, $exit, 'MySQL サーバ接続失敗', $this->host);
    }

    mysql_set_charset($this->encode); //文字コード設定
    if(! mysql_select_db($this->name, $db_handle)){ //データベース接続
      return $this->OutputError($header, $exit, 'データベース接続失敗', $this->name);
    }
    if($this->encode == 'utf8') mysql_query('SET NAMES utf8');
    return $this->db_handle = $db_handle; //成功したらハンドルを返して処理終了
  }

  //データベースとの接続を閉じる
  function Disconnect($unlock = false){
    if(is_null($this->db_handle)) return;

    if($unlock) UnlockTable(); //ロック解除
    mysql_close($this->db_handle);
    unset($this->db_handle); //ハンドルをクリア
  }

  //データベース名変更
  function ChangeName($id){
    if(is_null($name = $this->name_list[$id - 1])) return;
    $this->name = $name;
  }

  //エラー出力 ($header, $exit は Connect() 参照)
  private function OutputError($header, $exit, $title, $type){
    $str = $title . ': ' . $type; //エラーメッセージ作成
    if($header){
      echo '<font color="#FF0000">' . $str . '</font><br>';
      if($exit) OutputHTMLFooter($exit);
      return false;
    }
    OutputActionResult($title, $str);
  }
}

//-- セッション管理クラス --//
class Session{
  public $id;
  public $user_no;

  function __construct(){
    session_start();
    $this->Set();
  }

  //ID セット (private)
  function Set(){
    return $this->id = session_id();
  }

  //ID リセット
  function Reset(){
    session_regenerate_id();
    return $this->Set();
  }

  //ID 取得
  function Get($uniq = false){
    return $uniq ? $this->GetUniq() : $this->id;
  }

  //DB に登録されているセッション ID と被らないようにする (private)
  function GetUniq(){
    $query = 'SELECT COUNT(room_no) FROM user_entry WHERE session_id = ';
    do{
      $this->Reset();
    }while(FetchResult($query ."'{$this->id}'") > 0);
    return $this->id;
  }

  //認証したユーザの ID 取得
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
    $stack = FetchArray($query);
    if(count($stack) == 1){
      $this->user_no = $stack[0];
      return true;
    }

    if($exit) $this->OutputError(); //エラー処理
    return false;
  }

  //認証 (game_play 専用)
  function CertifyGamePlay(){
    global $RQ_ARGS;

    if($this->Certify(false)) return true;

    //村が存在するなら観戦ページにジャンプする
    if(FetchResult("SELECT COUNT(room_no) FROM room WHERE room_no = {$RQ_ARGS->room_no}") > 0){
      $url   = 'game_view.php?room_no=' . $RQ_ARGS->room_no;
      $title = '観戦ページにジャンプ';
      $body  = "観戦ページに移動します。<br>\n" .
	'切り替わらないなら <a href="' . $url . '" target="_top">ここ</a> 。'."\n" .
	'<script type="text/javascript"><!--'."\n" .
	'if(top != self){ top.location.href = self.location.href; }'."\n" .
	'--></script>'."\n";
      OutputActionResult($title, $body, $url);
    }
    else{
      $this->OutputError();
    }
  }

  //エラー出力
  private function OutputError(){
    $title = 'セッション認証エラー';
    $body  = $title . '：<a href="./" target="_top">トップページ</a>からログインしなおしてください';
    OutputActionResult($title, $body);
  }
}

//-- クッキーデータのロード処理 --//
class CookieDataSet{
  public $day_night;  //夜明け
  public $objection;  //「異議あり」の情報
  public $vote_times; //投票回数
  public $user_count; //参加人数

  function __construct(){
    $this->day_night  = $_COOKIE['day_night'];
    $this->objection  = $_COOKIE['objection'];
    $this->vote_times = (int)$_COOKIE['vote_times'];
    $this->user_count = (int)$_COOKIE['user_count'];
  }
}

//-- 外部リンク生成の基底クラス --//
class ExternalLinkBuilder{
  public $time = 2; //タイムアウト時間 (秒)

  //サーバ通信状態チェック (private)
  function CheckConnection($url){
    $url_stack = explode('/', $url);
    $this->host = $url_stack[2];
    if(! ($io = @fsockopen($this->host, 80, $status, $str, $this->time))) return false;

    stream_set_timeout($io, $this->time);
    fwrite($io, "GET / HTTP/1.1\r\nHost: {$host}\r\nConnection: Close\r\n\r\n");
    $data = fgets($io, 128);
    $stream_stack = stream_get_meta_data($io);
    fclose($io);
    //PrintData($data, 'Connection');
    return ! $stream_stack['timed_out'];
  }

  //HTML タグ生成
  function Generate($title, $data){
    return <<<EOF
<fieldset>
<legend>{$title}</legend>
<div class="game-list"><dl>{$data}</dl></div>
</fieldset>

EOF;
  }

  //BBS リンク生成
  function GenerateBBS($data){
    $title = '<a href="' . $this->view_url . $this->thread . 'l50' . '">告知スレッド情報</a>';
    return $this->Generate($title, $data);
  }

  //外部村リンク生成
  function GenerateSharedServerRoom($name, $url, $data){
    return $this->Generate('ゲーム一覧 (<a href="' . $url . '">' . $name . '</a>)', $data);
  }
}

//-- 村情報共有サーバ表示の基底クラス --//
class SharedServerConfigBase extends ExternalLinkBuilder{
  //他のサーバの部屋画面を出力
  function Output(){
    global $SERVER_CONF;

    if($this->disable) return false;

    foreach($this->server_list as $server => $array){
      extract($array);
      //PrintData($url, 'URL'); //テスト用
      if($disable) continue;

      if(! $this->CheckConnection($url)){ //サーバ通信状態チェック
	$data = $this->host . ": Connection timed out ({$this->time} seconds)";
	echo $this->GenerateSharedServerRoom($name, $url, $data);
	continue;
      }

      //部屋情報を取得
      if(($data = @file_get_contents($url.'room_manager.php')) == '') continue;
      //PrintData($data, 'Data'); //テスト用
      if($encode != '' && $encode != $this->encode){
	$data = mb_convert_encoding($data, $this->encode, $encode);
      }
      if($separator != ''){
	$split_list = mb_split($separator, $data);
	//PrintData($split_list, 'Split'); //テスト用
	$data = array_pop($split_list);
      }
      if($footer != ''){
	if(($position = mb_strrpos($data, $footer)) === false) continue;
	$data = mb_substr($data, 0, $position + mb_strlen($footer));
      }
      if($data == '') continue;

      $replace_list = array('href="' => 'href="' . $url, 'src="'  => 'src="' . $url);
      $data = strtr($data, $replace_list);
      echo $this->GenerateSharedServerRoom($name, $url, $data);
    }
  }
}

//-- 掲示板情報取得の基底クラス --//
class BBSConfigBase extends ExternalLinkBuilder{
  function Output(){
    global $SERVER_CONF;

    if($this->disable) return;
    if(! $this->CheckConnection($this->raw_url)){
      echo $this->GenerateBBS($this->host . ": Connection timed out ({$this->time} seconds)");
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

//ゲームプレイ時のアイコン表示設定の基底クラス --//
class IconConfigBase{
  //初期設定
  public $path   = 'user_icon'; //ユーザアイコンのパス
  public $dead   = 'grave.gif'; //死者
  public $wolf   = 'wolf.gif';  //狼
  public $width  = 45; //表示サイズ(幅)
  public $height = 45; //表示サイズ(高さ)

  function __construct(){
    $this->path = JINRO_ROOT . '/' . $this->path;
    $this->dead = JINRO_IMG  . '/' . $this->dead;
    $this->wolf = JINRO_IMG  . '/' . $this->wolf;
    $this->tag  = $this->GenerateTag();
  }

  function GenerateTag(){
    return ' width="' . $this->width . '" height="' . $this->height . '"';
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
  //画像のファイルパス取得 (private)
  function GetPath($name){
    return JINRO_IMG . '/' . $this->path . '/' . $name . '.' . $this->extension;
  }

  //画像の存在確認
  function Exists($name){
    return file_exists($this->GetPath($name));
  }

  //画像タグ生成
  function Generate($name, $alt = NULL, $table = false){
    $str = '<img';
    if($this->class != '') $str .= ' class="' . $this->class . '"';
    $str .= ' src="' . $this->GetPath($name) . '"';
    if(isset($alt)){
      EscapeStrings(&$alt);
      $str .= ' alt="' . $alt . '" title="' . $alt . '"';
    }
    $str .= '>';
    if($table) $str = '<td>' . $str . '</td>';
    return $str;
  }

  //画像出力
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

    case 'vampire':
      $alt = '吸血鬼勝利';
      break;

    case 'draw':
    case 'vanish':
    case 'quiz_dead':
      $name = 'draw';
      $alt = '引き分け';
      break;

    default:
      return '-';
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
  private function AddHeader($title){
    $this->str .= '<div class="menu">' . $title . "</div>\n<ul>\n";
  }

  //リンク生成
  private function AddLink($list){
    $header = $this->header . '<a href="';
    $footer = '</a>' . $this->footer;
    foreach($list as $name => $url) $this->str .= $header . $url . '">' . $name . $footer;
  }

  //フッタ追加
  private function AddFooter(){
    $this->str .= "</ul>\n";
  }
}

//-- Copyright 表示用の基底クラス --//
class CopyrightConfigBase{
  //投稿処理
  function Output(){
    global $SCRIPT_INFO;
    $stack = $this->list;
    foreach($this->add_list as $class => $list){
      $stack[$class] = array_key_exists($class, $stack) ?
	array_merge($stack[$class], $list) : $list;
    }

    foreach($stack as $class => $list){
      $str = '<h2>' . $class . "</h2>\n<ul>\n";
      foreach($list as $name => $url){
	$str .= '<li><a href="' . $url . '">' . $name . "</a></li>\n";
      }
      echo $str . "</ul>\n";
    }

    $php = PHP_VERSION;
    echo <<<EOF
<h2>パッケージ情報</h2>
<ul>
<li>PHP Ver. {$php}</li>
<li>{$SCRIPT_INFO->package} {$SCRIPT_INFO->version} (Rev. {$SCRIPT_INFO->revision})</li>
<li>LastUpdate: {$SCRIPT_INFO->last_update}</li>
</ul>

EOF;
  }
}

//-- 音源処理の基底クラス --//
class SoundBase{
  //音を鳴らす
  function Output($type){
    $path = JINRO_ROOT . '/' . $this->path . '/' . $this->$type . '.' . $this->extension;
    echo <<< EOF
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4,0,0,0" width="0" height="0">
<param name="movie" value="{$path}">
<param name="quality" value="high">
<embed src="{$path}" type="application/x-shockwave-flash" quality="high" width="0" height="0" loop="false" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
</embed>
</object>

EOF;
  }
}

//-- Twitter 投稿用の基底クラス --//
class TwitterConfigBase{
  //メッセージのセット
  function GenerateMessage($id, $name, $comment){
    return true;
  }

  //投稿処理
  function Send($id, $name, $comment){
    global $SERVER_CONF;
    if($this->disable) return;

    $message = $this->GenerateMessage($id, $name, $comment);
    //TwitterはUTF-8
    if($SERVER_CONF->encode != 'UTF-8'){
      $message = mb_convert_encoding($message, 'UTF-8', $SERVER_CONF->encode);
    }
    if(mb_strlen($message) > 140) $message = mb_substr($message, 0, 139);

    if($this->add_url){
      $url = $SERVER_CONF->site_root;
      if($this->direct_url) $url .= 'login.php?room_no=' . $id;
      if($this->short_url){
	$short_url = @file_get_contents('http://tinyurl.com/api-create.php?url=' . $url);
	if($short_url != '') $url = $short_url;
      }
      if(mb_strlen($message . $url) + 1 < 140) $message .= ' ' . $url;
    }
    if(strlen($this->hash) > 0 && mb_strlen($message . $this->hash) + 2 < 140){
      $message .= " #{$this->hash}";
    }

    //投稿
    $to = new TwitterOAuth($this->key_ck, $this->key_cs, $this->key_at, $this->key_as);
    $response = $to->OAuthRequest('https://twitter.com/statuses/update.json', 'POST',
				  array('status' => $message));

    if(! ($response === false || (strrpos($response, 'error')))) return;
    //エラー処理
    $sentence = 'Twitter への投稿に失敗しました。<br>'."\n" .
      'ユーザ名：' . $this->user . '<br>'."\n" . 'メッセージ：' . $message;
    PrintData($sentence);
  }
}

//-- ページ送りリンク生成クラス --//
class PageLinkBuilder{
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

  //表示するページのアドレスをセット (private)
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
  function GenerateTag($page, $title = NULL, $force = false){
    if($page == $this->page->set && ! $force) return '[' . $page . ']';
    if(is_null($title)) $title = '[' . $page . ']';
    if($this->file == 'index'){
      $footer = $page . '.html';
    }
    else{
      $list = $this->option;
      array_unshift($list, $this->type . '=' . $page);
      $footer = implode('&', $list);
    }
    return $this->url . $footer . '">' . $title . '</a>';
  }

  //ページリンクを生成する
  function Generate(){
    $url_stack = array('[' . $this->title . ']');
    if($this->file == 'index') $url_stack[] = '[<a href="index.html">new</a>]';
    if($this->page->start > 1 && $this->page->total > $this->view_page){
      $url_stack[] = $this->GenerateTag(1, '[1]...');
      $url_stack[] = $this->GenerateTag($this->page->start - 1, '&lt;&lt;');
    }

    for($i = $this->page->start; $i <= $this->page->end; $i++){
      $url_stack[] = $this->GenerateTag($i);
    }

    if($this->page->end < $this->page->total){
      $url_stack[] = $this->GenerateTag($this->page->end + 1, '&gt;&gt;');
      $url_stack[] = $this->GenerateTag($this->page->total, '...[' . $this->page->total . ']');
    }
    if($this->file != 'index') $url_stack[] = $this->GenerateTag('all');

    if($this->file == 'old_log'){
      $this->AddOption('reverse', $this->set_reverse ? 'off' : 'on');
      $url_stack[] = '[表示順]';
      $url_stack[] = $this->set_reverse ? '新↓古' : '古↓新';
      $name = ($this->set_reverse xor $this->reverse) ? '元に戻す' : '入れ替える';
      $url_stack[] =  $this->GenerateTag($this->page->set, $name, true);
    }
    return $this->header . implode(' ', $url_stack) . $this->footer;
  }

  //ページリンクを出力する
  function Output(){
    echo $this->Generate();
  }
}

//-- 役職データベース --//
class RoleData{
  //-- 役職名の翻訳 --//
  //メイン役職のリスト (コード名 => 表示名)
  //初日の役職通知リストはこの順番で表示される
  public $main_role_list = array(
    'human'                => '村人',
    'saint'                => '聖女',
    'executor'             => '執行者',
    'elder'                => '長老',
    'scripter'             => '執筆者',
    'suspect'              => '不審者',
    'unconscious'          => '無意識',
    'mage'                 => '占い師',
    'soul_mage'            => '魂の占い師',
    'psycho_mage'          => '精神鑑定士',
    'sex_mage'             => 'ひよこ鑑定士',
    'stargazer_mage'       => '占星術師',
    'voodoo_killer'        => '陰陽師',
    'dummy_mage'           => '夢見人',
    'necromancer'          => '霊能者',
    'soul_necromancer'     => '雲外鏡',
    'psycho_necromancer'   => '精神感応者',
    'embalm_necromancer'   => '死化粧師',
    'emissary_necromancer' => '密偵',
    'attempt_necromancer'  => '蟲姫',
    'yama_necromancer'     => '閻魔',
    'dummy_necromancer'    => '夢枕人',
    'medium'               => '巫女',
    'bacchus_medium'       => '神主',
    'seal_medium'          => '封印師',
    'revive_medium'        => '風祝',
    'priest'               => '司祭',
    'bishop_priest'        => '司教',
    'dowser_priest'        => '探知師',
    'weather_priest'       => '祈祷師',
    'high_priest'          => '大司祭',
    'crisis_priest'        => '預言者',
    'widow_priest'         => '未亡人',
    'revive_priest'        => '天人',
    'border_priest'        => '境界師',
    'dummy_priest'         => '夢司祭',
    'guard'                => '狩人',
    'hunter_guard'         => '猟師',
    'blind_guard'          => '夜雀',
    'gatekeeper_guard'     => '門番',
    'reflect_guard'        => '侍',
    'poison_guard'         => '騎士',
    'fend_guard'           => '忍者',
    'reporter'             => 'ブン屋',
    'anti_voodoo'          => '厄神',
    'dummy_guard'          => '夢守人',
    'common'               => '共有者',
    'detective_common'     => '探偵',
    'trap_common'          => '策士',
    'sacrifice_common'     => '首領',
    'ghost_common'         => '亡霊嬢',
    'dummy_common'         => '夢共有者',
    'poison'               => '埋毒者',
    'strong_poison'        => '強毒者',
    'incubate_poison'      => '潜毒者',
    'guide_poison'         => '誘毒者',
    'chain_poison'         => '連毒者',
    'dummy_poison'         => '夢毒者',
    'poison_cat'           => '猫又',
    'revive_cat'           => '仙狸',
    'sacrifice_cat'        => '猫神',
    'eclipse_cat'          => '蝕仙狸',
    'pharmacist'           => '薬師',
    'cure_pharmacist'      => '河童',
    'revive_pharmacist'    => '仙人',
    'alchemy_pharmacist'   => '錬金術師',
    'centaurus_pharmacist' => '人馬',
    'assassin'             => '暗殺者',
    'doom_assassin'        => '死神',
    'reverse_assassin'     => '反魂師',
    'soul_assassin'        => '辻斬り',
    'eclipse_assassin'     => '蝕暗殺者',
    'mind_scanner'         => 'さとり',
    'evoke_scanner'        => 'イタコ',
    'presage_scanner'      => '件',
    'clairvoyance_scanner' => '猩々',
    'whisper_scanner'      => '囁騒霊',
    'howl_scanner'         => '吠騒霊',
    'telepath_scanner'     => '念騒霊',
    'jealousy'             => '橋姫',
    'divorce_jealousy'     => '縁切地蔵',
    'priest_jealousy'      => '恋司祭',
    'poison_jealousy'      => '毒橋姫',
    'brownie'              => '座敷童子',
    'sun_brownie'          => '八咫烏',
    'revive_brownie'       => '蛇神',
    'cursed_brownie'       => '祟神',
    'history_brownie'      => '白澤',
    'wizard'               => '魔法使い',
    'soul_wizard'          => '八卦見',
    'awake_wizard'         => '比丘尼',
    'spiritism_wizard'     => '交霊術師',
    'barrier_wizard'       => '結界師',
    'astray_wizard'        => '左道使い',
    'pierrot_wizard'       => '道化師',
    'doll'                 => '上海人形',
    'friend_doll'          => '仏蘭西人形',
    'phantom_doll'         => '倫敦人形',
    'poison_doll'          => '鈴蘭人形',
    'doom_doll'            => '蓬莱人形',
    'revive_doll'          => '西蔵人形',
    'scarlet_doll'         => '和蘭人形',
    'silver_doll'          => '露西亜人形',
    'doll_master'          => '人形遣い',
    'escaper'              => '逃亡者',
    'psycho_escaper'       => '迷い人',
    'incubus_escaper'      => '一角獣',
    'succubus_escaper'     => '水妖姫',
    'doom_escaper'         => '半鳥女',
    'divine_escaper'       => '麒麟',
    'wolf'                 => '人狼',
    'boss_wolf'            => '白狼',
    'gold_wolf'            => '金狼',
    'phantom_wolf'         => '幻狼',
    'cursed_wolf'          => '呪狼',
    'wise_wolf'            => '賢狼',
    'poison_wolf'          => '毒狼',
    'resist_wolf'          => '抗毒狼',
    'revive_wolf'          => '仙狼',
    'trap_wolf'            => '狡狼',
    'blue_wolf'            => '蒼狼',
    'emerald_wolf'         => '翠狼',
    'sex_wolf'             => '雛狼',
    'tongue_wolf'          => '舌禍狼',
    'possessed_wolf'       => '憑狼',
    'hungry_wolf'          => '餓狼',
    'doom_wolf'            => '冥狼',
    'sirius_wolf'          => '天狼',
    'elder_wolf'           => '古狼',
    'cute_wolf'            => '萌狼',
    'scarlet_wolf'         => '紅狼',
    'silver_wolf'          => '銀狼',
    'mad'                  => '狂人',
    'fanatic_mad'          => '狂信者',
    'whisper_mad'          => '囁き狂人',
    'jammer_mad'           => '月兎',
    'voodoo_mad'           => '呪術師',
    'enchant_mad'          => '狢',
    'dream_eater_mad'      => '獏',
    'possessed_mad'        => '犬神',
    'trap_mad'             => '罠師',
    'snow_trap_mad'        => '雪女',
    'corpse_courier_mad'   => '火車',
    'amaze_mad'            => '傘化け',
    'agitate_mad'          => '扇動者',
    'miasma_mad'           => '土蜘蛛',
    'critical_mad'         => '釣瓶落とし',
    'follow_mad'           => '舟幽霊',
    'therian_mad'          => '獣人',
    'immolate_mad'         => '殉教者',
    'fox'                  => '妖狐',
    'white_fox'            => '白狐',
    'black_fox'            => '黒狐',
    'gold_fox'             => '金狐',
    'phantom_fox'          => '幻狐',
    'poison_fox'           => '管狐',
    'blue_fox'             => '蒼狐',
    'emerald_fox'          => '翠狐',
    'voodoo_fox'           => '九尾',
    'revive_fox'           => '仙狐',
    'possessed_fox'        => '憑狐',
    'doom_fox'             => '冥狐',
    'trap_fox'             => '狡狐',
    'cursed_fox'           => '天狐',
    'elder_fox'            => '古狐',
    'cute_fox'             => '萌狐',
    'scarlet_fox'          => '紅狐',
    'silver_fox'           => '銀狐',
    'immolate_fox'         => '野狐禅',
    'child_fox'            => '子狐',
    'sex_fox'              => '雛狐',
    'stargazer_fox'        => '星狐',
    'jammer_fox'           => '月狐',
    'miasma_fox'           => '蟲狐',
    'howl_fox'             => '化狐',
    'cupid'                => 'キューピッド',
    'self_cupid'           => '求愛者',
    'moon_cupid'           => 'かぐや姫',
    'mind_cupid'           => '女神',
    'sweet_cupid'          => '弁財天',
    'minstrel_cupid'       => '吟遊詩人',
    'triangle_cupid'       => '小悪魔',
    'angel'                => '天使',
    'rose_angel'           => '薔薇天使',
    'lily_angel'           => '百合天使',
    'exchange_angel'       => '魂移使',
    'ark_angel'            => '大天使',
    'sacrifice_angel'      => '守護天使',
    'scarlet_angel'        => '紅天使',
    'quiz'                 => '出題者',
    'vampire'              => '吸血鬼',
    'incubus_vampire'      => '青髭公',
    'succubus_vampire'     => '飛縁魔',
    'doom_vampire'         => '冥血鬼',
    'sacrifice_vampire'    => '吸血公',
    'soul_vampire'         => '吸血姫',
    'scarlet_vampire'      => '屍鬼',
    'chiroptera'           => '蝙蝠',
    'poison_chiroptera'    => '毒蝙蝠',
    'cursed_chiroptera'    => '呪蝙蝠',
    'boss_chiroptera'      => '大蝙蝠',
    'elder_chiroptera'     => '古蝙蝠',
    'cute_chiroptera'      => '萌蝙蝠',
    'scarlet_chiroptera'   => '紅蝙蝠',
    'dummy_chiroptera'     => '夢求愛者',
    'fairy'                => '妖精',
    'spring_fairy'         => '春妖精',
    'summer_fairy'         => '夏妖精',
    'autumn_fairy'         => '秋妖精',
    'winter_fairy'         => '冬妖精',
    'greater_fairy'        => '大妖精',
    'light_fairy'          => '光妖精',
    'dark_fairy'           => '闇妖精',
    'grass_fairy'          => '草妖精',
    'sun_fairy'            => '日妖精',
    'moon_fairy'           => '月妖精',
    'star_fairy'           => '星妖精',
    'flower_fairy'         => '花妖精',
    'shadow_fairy'         => '影妖精',
    'mirror_fairy'         => '鏡妖精',
    'ice_fairy'            => '氷妖精',
    'ogre'                 => '鬼',
    'orange_ogre'          => '前鬼',
    'indigo_ogre'          => '後鬼',
    'poison_ogre'          => '榊鬼',
    'west_ogre'            => '金鬼',
    'east_ogre'            => '風鬼',
    'north_ogre'           => '水鬼',
    'south_ogre'           => '隠行鬼',
    'incubus_ogre'         => '般若',
    'power_ogre'           => '星熊童子',
    'revive_ogre'          => '茨木童子',
    'sacrifice_ogre'       => '酒呑童子',
    'yaksa'                => '夜叉',
    'betray_yaksa'         => '夜叉丸',
    'cursed_yaksa'         => '滝夜叉姫',
    'succubus_yaksa'       => '荼枳尼天',
    'dowser_yaksa'         => '毘沙門天',
    'duelist'              => '決闘者',
    'valkyrja_duelist'     => '戦乙女',
    'doom_duelist'         => '黒幕',
    'critical_duelist'     => '剣闘士',
    'triangle_duelist'     => '舞首',
    'avenger'              => '復讐者',
    'poison_avenger'       => '山わろ',
    'cursed_avenger'       => 'がしゃどくろ',
    'critical_avenger'     => '狂骨',
    'revive_avenger'       => '夜刀神',
    'cute_avenger'         => '草履大将',
    'patron'               => '後援者',
    'soul_patron'          => '家神',
    'sacrifice_patron'     => '身代わり地蔵',
    'shepherd_patron'      => '羊飼い',
    'mania'                => '神話マニア',
    'trick_mania'          => '奇術師',
    'basic_mania'          => '求道者',
    'soul_mania'           => '覚醒者',
    'dummy_mania'          => '夢語部',
    'unknown_mania'        => '鵺',
    'sacrifice_mania'      => '影武者',
    'wirepuller_mania'     => '黒衣');

  //サブ役職のリスト (コード名 => 表示名)
  //初日の役職通知リストはこの順番で表示される
  public $sub_role_list = array(
    'chicken'            => '小心者',
    'rabbit'             => 'ウサギ',
    'perverseness'       => '天邪鬼',
    'flattery'           => 'ゴマすり',
    'celibacy'           => '独身貴族',
    'nervy'              => '自信家',
    'androphobia'        => '男性恐怖症',
    'gynophobia'         => '女性恐怖症',
    'impatience'         => '短気',
    'febris'             => '熱病',
    'frostbite'          => '凍傷',
    'death_warrant'      => '死の宣告',
    'panelist'           => '解答者',
    'liar'               => '狼少年',
    'actor'              => '役者',
    'passion'            => '恋色迷彩',
    'rainbow'            => '虹色迷彩',
    'weekly'             => '七曜迷彩',
    'grassy'             => '草原迷彩',
    'invisible'          => '光学迷彩',
    'side_reverse'       => '鏡面迷彩',
    'line_reverse'       => '天地迷彩',
    'gentleman'          => '紳士',
    'lady'               => '淑女',
    'decide'             => '決定者',
    'plague'             => '疫病神',
    'counter_decide'     => '燕返し',
    'dropout'            => '脱落者',
    'good_luck'          => '幸運',
    'bad_luck'           => '不運',
    'authority'          => '権力者',
    'reduce_voter'       => '無精者',
    'upper_voter'        => 'わらしべ長者',
    'downer_voter'       => '没落者',
    'critical_voter'     => '会心',
    'rebel'              => '反逆者',
    'random_voter'       => '気分屋',
    'watcher'            => '傍観者',
    'day_voter'          => '一日村長',
    'upper_luck'         => '雑草魂',
    'downer_luck'        => '一発屋',
    'star'               => '人気者',
    'disfavor'           => '不人気',
    'critical_luck'      => '痛恨',
    'random_luck'        => '波乱万丈',
    'strong_voice'       => '大声',
    'normal_voice'       => '不器用',
    'weak_voice'         => '小声',
    'inside_voice'       => '内弁慶',
    'outside_voice'      => '外弁慶',
    'upper_voice'        => 'メガホン',
    'downer_voice'       => 'マスク',
    'random_voice'       => '臆病者',
    'no_last_words'      => '筆不精',
    'blinder'            => '目隠し',
    'earplug'            => '耳栓',
    'speaker'            => 'スピーカー',
    'whisper_ringing'    => '囁耳鳴',
    'howl_ringing'       => '吠耳鳴',
    'sweet_ringing'      => '恋耳鳴',
    'deep_sleep'         => '爆睡者',
    'silent'             => '無口',
    'mower'              => '草刈り',
    'mind_read'          => 'サトラレ',
    'mind_open'          => '公開者',
    'mind_receiver'      => '受信者',
    'mind_friend'        => '共鳴者',
    'mind_sympathy'      => '共感者',
    'mind_evoke'         => '口寄せ',
    'mind_presage'       => '受託者',
    'mind_lonely'        => 'はぐれ者',
    'mind_sheep'         => '羊',
    'sheep_wisp'         => '羊皮',
    'lovers'             => '恋人',
    'challenge_lovers'   => '難題',
    'possessed_exchange' => '交換憑依',
    'infected'           => '感染者',
    'psycho_infected'    => '洗脳者',
    'joker'              => 'ジョーカー',
    'rival'              => '宿敵',
    'enemy'              => '仇敵',
    'supported'          => '受援者',
    'possessed_target'   => '憑依者',
    'possessed'          => '憑依',
    'bad_status'         => '悪戯',
    'protected'          => '庇護者',
    'wirepuller_luck'    => '入道',
    'lost_ability'       => '能力喪失',
    'muster_ability'     => '能力発現',
    'changed_therian'    => '元獣人',
    'copied'             => '元神話マニア',
    'copied_trick'       => '元奇術師',
    'copied_basic'       => '元求道者',
    'copied_soul'        => '元覚醒者',
    'copied_teller'      => '元夢語部');

  //役職の省略名 (過去ログ用)
  public $short_role_list = array(
    'human'                => '村',
    'saint'                => '聖',
    'executor'             => '執行',
    'elder'                => '老',
    'scripter'             => '執筆',
    'suspect'              => '不審',
    'unconscious'          => '無',
    'mage'                 => '占',
    'soul_mage'            => '魂',
    'psycho_mage'          => '心占',
    'sex_mage'             => '雛占',
    'stargazer_mage'       => '星占',
    'voodoo_killer'        => '陰陽',
    'dummy_mage'           => '夢見',
    'necromancer'          => '霊',
    'soul_necromancer'     => '雲',
    'psycho_necromancer'   => '心霊',
    'embalm_necromancer'   => '粧',
    'emissary_necromancer' => '密',
    'attempt_necromancer'  => '蟲姫',
    'yama_necromancer'     => '閻',
    'dummy_necromancer'    => '夢枕',
    'medium'               => '巫',
    'bacchus_medium'       => '神主',
    'seal_medium'          => '封',
    'revive_medium'        => '風',
    'priest'               => '司',
    'bishop_priest'        => '教',
    'dowser_priest'        => '探',
    'weather_priest'       => '祈',
    'high_priest'          => '大司',
    'crisis_priest'        => '預',
    'widow_priest'         => '未',
    'revive_priest'        => '天人',
    'border_priest'        => '境',
    'dummy_priest'         => '夢司',
    'guard'                => '狩',
    'hunter_guard'         => '猟',
    'blind_guard'          => '雀',
    'gatekeeper_guard'     => '門',
    'reflect_guard'        => '侍',
    'poison_guard'         => '騎',
    'fend_guard'           => '忍',
    'reporter'             => '聞',
    'anti_voodoo'          => '厄',
    'dummy_guard'          => '夢守',
    'common'               => '共',
    'detective_common'     => '偵',
    'trap_common'          => '策',
    'sacrifice_common'     => '領',
    'ghost_common'         => '亡',
    'dummy_common'         => '夢共',
    'poison'               => '毒',
    'strong_poison'        => '強毒',
    'incubate_poison'      => '潜毒',
    'guide_poison'         => '誘毒',
    'chain_poison'         => '連毒',
    'dummy_poison'         => '夢毒',
    'poison_cat'           => '猫',
    'revive_cat'           => '仙狸',
    'sacrifice_cat'        => '猫神',
    'eclipse_cat'          => '蝕狸',
    'pharmacist'           => '薬',
    'cure_pharmacist'      => '河',
    'revive_pharmacist'    => '仙人',
    'alchemy_pharmacist'   => '錬',
    'centaurus_pharmacist' => '馬',
    'assassin'             => '暗',
    'doom_assassin'        => '死神',
    'reverse_assassin'     => '反魂',
    'soul_assassin'        => '辻',
    'eclipse_assassin'     => '蝕暗',
    'mind_scanner'         => '覚',
    'evoke_scanner'        => 'イ',
    'presage_scanner'      => '件',
    'clairvoyance_scanner' => '猩',
    'whisper_scanner'      => '囁騒',
    'howl_scanner'         => '吠騒',
    'telepath_scanner'     => '念騒',
    'jealousy'             => '橋',
    'divorce_jealousy'     => '縁切',
    'priest_jealousy'      => '恋司',
    'poison_jealousy'      => '毒橋',
    'brownie'              => '童',
    'sun_brownie'          => '烏',
    'revive_brownie'       => '蛇',
    'cursed_brownie'       => '祟',
    'history_brownie'      => '澤',
    'wizard'               => '魔',
    'soul_wizard'          => '八',
    'awake_wizard'         => '尼',
    'spiritism_wizard'     => '交',
    'barrier_wizard'       => '結',
    'astray_wizard'        => '左',
    'pierrot_wizard'       => '道',
    'doll'                 => '海',
    'friend_doll'          => '仏',
    'phantom_doll'         => '倫',
    'poison_doll'          => '鈴',
    'doom_doll'            => '蓬',
    'revive_doll'          => '西',
    'scarlet_doll'         => '蘭',
    'silver_doll'          => '露',
    'doll_master'          => '遣',
    'escaper'              => '逃',
    'psycho_escaper'       => '迷',
    'incubus_escaper'      => '角',
    'succubus_escaper'     => '水',
    'doom_escaper'         => '半',
    'divine_escaper'       => '麒',
    'wolf'                 => '狼',
    'boss_wolf'            => '白狼',
    'gold_wolf'            => '金狼',
    'phantom_wolf'         => '幻狼',
    'cursed_wolf'          => '呪狼',
    'wise_wolf'            => '賢狼',
    'poison_wolf'          => '毒狼',
    'resist_wolf'          => '抗狼',
    'revive_wolf'          => '仙狼',
    'trap_wolf'            => '狡狼',
    'blue_wolf'            => '蒼狼',
    'emerald_wolf'         => '翠狼',
    'sex_wolf'             => '雛狼',
    'tongue_wolf'          => '舌狼',
    'possessed_wolf'       => '憑狼',
    'hungry_wolf'          => '餓狼',
    'doom_wolf'            => '冥狼',
    'sirius_wolf'          => '天狼',
    'elder_wolf'           => '古狼',
    'cute_wolf'            => '萌狼',
    'scarlet_wolf'         => '紅狼',
    'silver_wolf'          => '銀狼',
    'mad'                  => '狂',
    'fanatic_mad'          => '狂信',
    'whisper_mad'          => '囁狂',
    'jammer_mad'           => '月兎',
    'voodoo_mad'           => '呪狂',
    'enchant_mad'          => '狢',
    'dream_eater_mad'      => '獏',
    'possessed_mad'        => '犬',
    'trap_mad'             => '罠',
    'snow_trap_mad'        => '雪',
    'corpse_courier_mad'   => '火',
    'amaze_mad'            => '傘',
    'agitate_mad'          => '扇',
    'miasma_mad'           => '蜘',
    'critical_mad'         => '釣',
    'follow_mad'           => '舟',
    'therian_mad'          => '獣',
    'immolate_mad'         => '殉',
    'fox'                  => '狐',
    'white_fox'            => '白狐',
    'black_fox'            => '黒狐',
    'gold_fox'             => '金狐',
    'phantom_fox'          => '幻狐',
    'poison_fox'           => '管狐',
    'blue_fox'             => '蒼狐',
    'emerald_fox'          => '翠狐',
    'voodoo_fox'           => '九尾',
    'revive_fox'           => '仙狐',
    'possessed_fox'        => '憑狐',
    'doom_fox'             => '冥狐',
    'trap_fox'             => '狡狐',
    'cursed_fox'           => '天狐',
    'elder_fox'            => '古狐',
    'cute_fox'             => '萌狐',
    'scarlet_fox'          => '紅狐',
    'silver_fox'           => '銀狐',
    'immolate_fox'         => '野狐',
    'child_fox'            => '子狐',
    'sex_fox'              => '雛狐',
    'stargazer_fox'        => '星狐',
    'jammer_fox'           => '月狐',
    'miasma_fox'           => '蟲狐',
    'howl_fox'             => '化狐',
    'cupid'                => 'QP',
    'self_cupid'           => '求',
    'moon_cupid'           => '姫',
    'mind_cupid'           => '女神',
    'sweet_cupid'          => '弁',
    'minstrel_cupid'       => '吟',
    'triangle_cupid'       => '小悪',
    'angel'                => '天使',
    'rose_angel'           => '薔天',
    'lily_angel'           => '百天',
    'exchange_angel'       => '魂移',
    'ark_angel'            => '大天',
    'sacrifice_angel'      => '守天',
    'scarlet_angel'        => '紅天',
    'quiz'                 => 'GM',
    'vampire'              => '血',
    'incubus_vampire'      => '髭',
    'succubus_vampire'     => '飛',
    'doom_vampire'         => '冥血',
    'sacrifice_vampire'    => '血公',
    'soul_vampire'         => '血姫',
    'scarlet_vampire'      => '屍',
    'chiroptera'           => '蝙',
    'poison_chiroptera'    => '毒蝙',
    'cursed_chiroptera'    => '呪蝙',
    'boss_chiroptera'      => '大蝙',
    'elder_chiroptera'     => '古蝙',
    'cute_chiroptera'      => '萌蝙',
    'scarlet_chiroptera'   => '紅蝙',
    'dummy_chiroptera'     => '夢愛',
    'fairy'                => '妖精',
    'spring_fairy'         => '春精',
    'summer_fairy'         => '夏精',
    'autumn_fairy'         => '秋精',
    'winter_fairy'         => '冬精',
    'greater_fairy'        => '大精',
    'light_fairy'          => '光精',
    'dark_fairy'           => '闇精',
    'grass_fairy'          => '草精',
    'sun_fairy'            => '日精',
    'moon_fairy'           => '月精',
    'star_fairy'           => '星精',
    'flower_fairy'         => '花精',
    'shadow_fairy'         => '影精',
    'mirror_fairy'         => '鏡精',
    'ice_fairy'            => '氷精',
    'ogre'                 => '鬼',
    'orange_ogre'          => '前鬼',
    'indigo_ogre'          => '後鬼',
    'poison_ogre'          => '榊鬼',
    'west_ogre'            => '金鬼',
    'east_ogre'            => '風鬼',
    'north_ogre'           => '水鬼',
    'south_ogre'           => '隠鬼',
    'incubus_ogre'         => '般',
    'power_ogre'           => '熊',
    'revive_ogre'          => '茨',
    'sacrifice_ogre'       => '酒',
    'yaksa'                => '夜',
    'betray_yaksa'         => '叉丸',
    'cursed_yaksa'         => '滝',
    'succubus_yaksa'       => '荼',
    'dowser_yaksa'         => '毘',
    'duelist'              => '闘',
    'valkyrja_duelist'     => '戦',
    'doom_duelist'         => '幕',
    'critical_duelist'     => '剣',
    'triangle_duelist'     => '舞',
    'avenger'              => '讐',
    'poison_avenger'       => '山',
    'cursed_avenger'       => '餓紗',
    'critical_avenger'     => '骨',
    'revive_avenger'       => '夜刀',
    'cute_avenger'         => '草履',
    'patron'               => '後援',
    'soul_patron'          => '家',
    'sacrifice_patron'     => '地蔵',
    'shepherd_patron'      => '羊飼',
    'mania'                => 'マ',
    'trick_mania'          => '奇',
    'basic_mania'          => '求道',
    'soul_mania'           => '覚醒',
    'dummy_mania'          => '夢語',
    'unknown_mania'        => '鵺',
    'sacrifice_mania'      => '影',
    'wirepuller_mania'     => '衣',
    'chicken'              => '酉',
    'rabbit'               => '卯',
    'perverseness'         => '邪',
    'flattery'             => '胡麻',
    'celibacy'             => '独',
    'nervy'                => '信',
    'androphobia'          => '男恐',
    'gynophobia'           => '女恐',
    'impatience'           => '短',
    'febris'               => '熱',
    'frostbite'            => '凍',
    'death_warrant'        => '宣',
    'panelist'             => '解',
    'liar'                 => '嘘',
    'actor'                => '役',
    'passion'              => '恋迷',
    'rainbow'              => '虹迷',
    'weekly'               => '曜迷',
    'grassy'               => '草迷',
    'invisible'            => '光迷',
    'side_reverse'         => '鏡迷',
    'line_reverse'         => '天迷',
    'gentleman'            => '紳',
    'lady'                 => '淑',
    'decide'               => '決',
    'plague'               => '疫',
    'counter_decide'       => '燕',
    'dropout'              => '脱',
    'good_luck'            => '幸',
    'bad_luck'             => '不運',
    'authority'            => '権',
    'reduce_voter'         => '無精',
    'upper_voter'          => '藁',
    'downer_voter'         => '没',
    'critical_voter'       => '会',
    'rebel'                => '反',
    'random_voter'         => '気',
    'watcher'              => '傍',
    'day_voter'            => '日権',
    'upper_luck'           => '雑',
    'downer_luck'          => '一発',
    'star'                 => '人気',
    'disfavor'             => '不人',
    'critical_luck'        => '痛',
    'random_luck'          => '乱',
    'strong_voice'         => '大',
    'normal_voice'         => '不',
    'weak_voice'           => '小',
    'inside_voice'         => '内弁',
    'outside_voice'        => '外弁',
    'upper_voice'          => '拡声',
    'downer_voice'         => '覆',
    'random_voice'         => '臆',
    'no_last_words'        => '筆',
    'blinder'              => '目',
    'earplug'              => '耳',
    'speaker'              => '集音',
    'whisper_ringing'      => '囁鳴',
    'howl_ringing'         => '吠鳴',
    'sweet_ringing'        => '恋鳴',
    'deep_sleep'           => '爆睡',
    'silent'               => '無口',
    'mower'                => '草刈',
    'mind_read'            => '漏',
    'mind_evoke'           => '口寄',
    'mind_presage'         => '託',
    'mind_open'            => '公',
    'mind_receiver'        => '受',
    'mind_friend'          => '鳴',
    'mind_sympathy'        => '感',
    'mind_lonely'          => '逸',
    'mind_sheep'           => '羊',
    'sheep_wisp'           => '羊皮',
    'lovers'               => '恋',
    'challenge_lovers'     => '難',
    'possessed_exchange'   => '換',
    'infected'             => '染',
    'psycho_infected'      => '洗',
    'joker'                => '道化',
    'rival'                => '宿',
    'enemy'                => '仇',
    'supported'            => '援',
    'possessed_target'     => '憑',
    'possessed'            => '被憑',
    'bad_status'           => '戯',
    'protected'            => '庇',
    'wirepuller_luck'      => '入道',
    'lost_ability'         => '失',
    'muster_ability'       => '発',
    'changed_therian'      => '元獣',
    'copied'               => '元マ',
    'copied_trick'         => '元奇',
    'copied_basic'         => '元求',
    'copied_soul'          => '元覚',
    'copied_teller'        => '元語');

  //メイン役職のグループリスト (役職 => 所属グループ)
  //このリストの並び順に strpos() で判別する (毒系など、順番依存の役職があるので注意)
  public $main_role_group_list = array(
    'wolf' => 'wolf',
    'mad' => 'mad',
    'child_fox' => 'child_fox', 'sex_fox' => 'child_fox', 'stargazer_fox' => 'child_fox',
    'jammer_fox' => 'child_fox', 'miasma_fox' => 'child_fox', 'howl_fox' => 'child_fox',
    'fox' => 'fox',
    'cupid' => 'cupid',
    'angel' => 'angel',
    'quiz' => 'quiz',
    'vampire' => 'vampire',
    'chiroptera' => 'chiroptera',
    'fairy' => 'fairy',
    'ogre' => 'ogre',
    'yaksa' => 'yaksa',
    'duelist' => 'duelist',
    'avenger' => 'avenger',
    'patron' => 'patron',
    'mage' => 'mage', 'voodoo_killer' => 'mage',
    'necromancer' => 'necromancer',
    'medium' => 'medium',
    'jealousy' => 'jealousy',
    'priest' => 'priest',
    'guard' => 'guard', 'anti_voodoo' => 'guard', 'reporter' => 'guard',
    'common' => 'common',
    'cat' => 'poison_cat',
    'pharmacist' => 'pharmacist',
    'assassin' => 'assassin',
    'scanner' => 'mind_scanner',
    'brownie' => 'brownie',
    'wizard' => 'wizard',
    'doll' => 'doll',
    'escaper' => 'escaper',
    'poison' => 'poison',
    'unknown_mania' => 'unknown_mania', 'sacrifice_mania' => 'unknown_mania',
    'wirepuller_mania' => 'unknown_mania',
    'mania' => 'mania');

  //サブ役職のグループリスト (CSS のクラス名 => 所属役職)
  //このリストの表示順に PlayerList の役職が表示される
  public $sub_role_group_list = array(
    'lovers'       => array('lovers', 'challenge_lovers', 'possessed_exchange'),
    'duelist'      => array('rival', 'enemy', 'supported'),
    'mind'         => array('mind_read', 'mind_open', 'mind_receiver', 'mind_friend',
			    'mind_sympathy', 'mind_evoke', 'mind_presage', 'mind_lonely',
			    'mind_sheep'),
    'vampire'      => array('infected', 'psycho_infected'),
    'sudden-death' => array('chicken', 'rabbit', 'perverseness', 'flattery', 'celibacy', 'nervy',
			    'androphobia', 'gynophobia', 'impatience', 'febris', 'frostbite',
			    'death_warrant', 'panelist'),
    'convert'      => array('liar', 'actor', 'passion', 'rainbow', 'weekly', 'grassy', 'invisible',
			    'side_reverse', 'line_reverse', 'gentleman', 'lady'),
    'decide'       => array('decide', 'plague', 'counter_decide', 'dropout', 'good_luck',
			    'bad_luck'),
    'authority'    => array('authority', 'reduce_voter', 'upper_voter', 'downer_voter',
			    'critical_voter', 'rebel', 'random_voter', 'watcher', 'day_voter',
			    'wirepuller_luck'),
    'luck'         => array('upper_luck', 'downer_luck', 'star', 'disfavor', 'critical_luck',
			    'random_luck'),
    'voice'        => array('strong_voice', 'normal_voice', 'weak_voice', 'upper_voice',
			    'downer_voice', 'inside_voice', 'outside_voice', 'random_voice'),
    'seal'         => array('no_last_words', 'blinder', 'earplug', 'speaker', 'whisper_ringing',
			    'howl_ringing', 'sweet_ringing', 'deep_sleep', 'silent', 'mower'),
    'mage'         => array('sheep_wisp'),
    'guard'        => array('protected'),
    'chiroptera'   => array('joker', 'bad_status'),
    'human'        => array('lost_ability', 'muster_ability'),
    'wolf'         => array('possessed_target', 'possessed', 'changed_therian'),
    'mania'        => array('copied', 'copied_trick', 'copied_basic', 'copied_soul', 'copied_teller'));

  //天候のリスト
  public $weather_list = array(
     0 => array('name'    => 'スコール',
		'event'   => 'grassy',
		'caption' => '全員 草原迷彩'),
     1 => array('name'    => '酸性雨',
		'event'   => 'mower',
		'caption' => '全員 草刈り'),
     2 => array('name'    => '晴嵐',
		'event'   => 'blind_vote',
		'caption' => '処刑投票結果隠蔽'),
     3 => array('name'    => '天気雨',
		'event'   => 'no_fox_dead',
		'caption' => '呪殺無し'),
     4 => array('name'    => '烈日',
		'event'   => 'critical',
		'caption' => '会心・痛恨 発動率 100%'),
     5 => array('name'    => '強風',
		'event'   => 'blind_talk_day',
		'caption' => '昼会話妨害'),
     6 => array('name'    => '風雨',
		'event'   => 'blind_talk_night',
		'caption' => '夜会話妨害'),
     7 => array('name'    => '満月',
		'event'   => 'full_moon',
		'caption' => '占い妨害・呪術・狩人系 封印'),
     8 => array('name'    => '新月',
		'event'   => 'new_moon',
		'caption' => '占い・魔法・人狼・吸血・悪戯 封印'),
     9 => array('name'    => '花曇',
		'event'   => 'no_contact',
		'caption' => '接触系封印'),
    10 => array('name'    => '黄砂',
		'event'   => 'invisible',
		'caption' => '全員 光学迷彩'),
    11 => array('name'    => '虹',
		'event'   => 'rainbow',
		'caption' => '全員 虹色迷彩'),
    12 => array('name'    => 'ダイヤモンドダスト',
		'event'   => 'side_reverse',
		'caption' => '全員 鏡面迷彩'),
    13 => array('name'    => 'バナナの皮',
		'event'   => 'line_reverse',
		'caption' => '全員 天地迷彩'),
    14 => array('name'    => 'スポットライト',
		'event'   => 'actor',
		'caption' => '全員 役者'),
    15 => array('name'    => 'タライ',
		'event'   => 'critical_luck',
		'caption' => '全員 痛恨'),
    16 => array('name'    => '凪',
		'event'   => 'no_sudden_death',
		'caption' => 'ショック死サブ無効'),
    17 => array('name'    => '青天の霹靂',
		'event'   => 'thunderbolt',
		'caption' => 'ショック死発生'),
    18 => array('name'    => '涙雨',
		'event'   => 'no_last_words',
		'caption' => '全員 筆不精'),
    19 => array('name'    => '熱帯夜',
		'event'   => 'no_dream',
		'caption' => '夢能力封印'),
    20 => array('name'    => '朧月',
		'event'   => 'full_ogre',
		'caption' => '鬼強化'),
    21 => array('name'    => '叢雲',
		'event'   => 'seal_ogre',
		'caption' => '鬼封印'),
    22 => array('name'    => '雷雨',
		'event'   => 'full_revive',
		'caption' => '蘇生強化'),
    23 => array('name'    => '快晴',
		'event'   => 'no_revive',
		'caption' => '蘇生封印'),
    24 => array('name'    => '慈雨',
		'event'   => 'brownie',
		'caption' => '村人 投票数 +1'),
    25 => array('name'    => '波風',
		'event'   => 'whisper_ringing',
		'caption' => '全員 囁耳鳴'),
    26 => array('name'    => '小夜嵐',
		'event'   => 'howl_ringing',
		'caption' => '全員 吠耳鳴'),
    27 => array('name'    => '流星群',
		'event'   => 'sweet_ringing',
		'caption' => '全員 恋耳鳴'),
    28 => array('name'    => '春時雨',
		'event'   => 'deep_sleep',
		'caption' => '全員 爆睡者'),
    29 => array('name'    => '木漏れ日',
		'event'   => 'silent',
		'caption' => '全員 無口'),
    30 => array('name'    => '疎雨',
		'event'   => 'missfire_revive',
		'caption' => '蘇生誤爆率上昇'),
    31 => array('name'    => '川霧',
		'event'   => 'no_hunt',
		'caption' => '護衛狩り無し'),
    32 => array('name'    => '蒼天',
		'event'   => 'full_guard',
		'caption' => '護衛制限無し'),
    33 => array('name'    => '雪',
		'event'   => 'frostbite',
		'caption' => '凍傷付加'),
    34 => array('name'    => '梅雨',
		'event'   => 'alchemy_pharmacist',
		'caption' => '毒強化'),
    35 => array('name'    => '雹',
		'event'   => 'hyper_random_voter',
		'caption' => '投票数ランダム増加'),
    36 => array('name'    => '半月',
		'event'   => 'half_moon',
		'caption' => '占い妨害'),
    37 => array('name'    => '曇天',
		'event'   => 'half_guard',
		'caption' => '護衛成功率低下'),
    38 => array('name'    => '箒星',
		'event'   => 'passion',
		'caption' => '全員 恋色迷彩'),
    39 => array('name'    => '旱魃',
		'event'   => 'no_poison',
		'caption' => '毒無し'),
    40 => array('name'    => '濃霧',
		'event'   => 'psycho_infected',
		'caption' => '洗脳者付加'),
    41 => array('name'    => '台風',
		'event'   => 'hyper_critical',
		'caption' => '全員 会心・痛恨'),
    42 => array('name'    => '萌動',
		'event'   => 'boost_cute',
		'caption' => '萌え発動率上昇'),
    43 => array('name'    => '蜃気楼',
		'event'   => 'no_authority',
		'caption' => '投票/得票数補正サブ無効'),
    44 => array('name'    => '紅月',
		'event'   => 'force_assassin_do',
		'caption' => '暗殺キャンセル投票不可'),
    45 => array('name'    => '砂塵嵐',
		'event'   => 'corpse_courier_mad',
		'caption' => '霊能妨害'),
    46 => array('name'    => '霧雨',
		'event'   => 'full_wizard',
		'caption' => '魔法強化'),
    47 => array('name'    => '木枯らし',
		'event'   => 'debilitate_wizard',
		'caption' => '魔法弱体化'),
    48 => array('name'    => '雪明り',
		'event'   => 'no_trap',
		'caption' => '罠封印'),
    49 => array('name'    => '蛍火',
		'event'   => 'no_sacrifice',
		'caption' => '身代わり封印'),
    50 => array('name'    => '日蝕',
		'event'   => 'no_reflect_assassin',
		'caption' => '暗殺反射封印'),
    51 => array('name'    => '月蝕',
		'event'   => 'no_cursed',
		'caption' => '呪返し封印'),
    52 => array('name'    => '宵闇',
		'event'   => 'blinder',
		'caption' => '全員 目隠し'),
    53 => array('name'    => '白夜',
		'event'   => 'mind_open',
		'caption' => '全員 公開者'),
    54 => array('name'    => '極光',
		'event'   => 'aurora',
		'caption' => '全員 目隠し・公開者'));

  //-- 関数 --//
  //役職グループ判定
  function DistinguishRoleGroup($role){
    foreach($this->main_role_group_list as $key => $value){
      if(strpos($role, $key) !== false) return $value;
    }
    return 'human';
  }

  //所属陣営判別
  function DistinguishCamp($role, $start = false){
    switch($camp = $this->DistinguishRoleGroup($role)){
    case 'wolf':
    case 'mad':
      return 'wolf';

    case 'fox':
    case 'child_fox':
      return 'fox';

    case 'cupid':
    case 'angel':
      return $start ? 'cupid' : 'lovers';

    case 'quiz':
    case 'vampire':
      return $camp;

    case 'chiroptera':
    case 'fairy':
      return 'chiroptera';

    case 'ogre':
    case 'yaksa':
      return 'ogre';

    case 'duelist':
    case 'avenger':
    case 'patron':
      return 'duelist';

    case 'mania':
    case 'unknown_mania':
      return $start ? 'mania' : 'human';

    default:
      return 'human';
    }
  }

  //役職クラス (CSS) 判定
  function DistinguishRoleClass($role){
    switch($class = $this->DistinguishRoleGroup($role)){
    case 'poison_cat':
      $class = 'cat';
      break;

    case 'mind_scanner':
      $class = 'mind';
      break;

    case 'child_fox':
      $class = 'fox';
      break;

    case 'unknown_mania':
      $class = 'mania';
      break;
    }
    return $class;
  }

  //役職名のタグ生成
  function GenerateRoleTag($role, $css = NULL, $sub_role = false){
    $str = '';
    if(is_null($css)) $css = $this->DistinguishRoleClass($role);
    if($sub_role) $str .= '<br>';
    $str .= '<span class="' . $css . '">[' .
      ($sub_role ? $this->sub_role_list[$role] : $this->main_role_list[$role]) . ']</span>';
    return $str;
  }

  //役職名のタグ生成 (メイン役職専用)
  function GenerateMainRoleTag($role, $tag = 'span'){
    return '<' . $tag . ' class="' . $this->DistinguishRoleClass($role) . '">' .
      $this->main_role_list[$role] . '</' . $tag .'>';
  }
}

//-- 「福引」生成の基底クラス --//
class LotteryBuilder{
  //「福引き」を一定回数行ってリストに追加する
  function AddRandom(&$list, $random_list, $count){
    $total = count($random_list) - 1;
    for(; $count > 0; $count--) $list[$random_list[mt_rand(0, $total)]]++;
  }

  //「比」の配列から「福引き」を作成する
  function GenerateRandomList($list){
    $stack = array();
    foreach($list as $role => $rate){
      for(; $rate > 0; $rate--) $stack[] = $role;
    }
    return $stack;
  }

  //「比」から「確率」に変換する (テスト用)
  function RateToProbability($list){
    $stack = array();
    $total_rate = array_sum($list);
    foreach($list as $role => $rate){
      $stack[$role] = sprintf('%01.2f', $rate / $total_rate * 100);
    }
    PrintData($stack);
  }
}

//-- 配役設定の基底クラス --//
class GameConfigBase extends LotteryBuilder{
  //天候決定
  function GetWeather(){
    return GetRandom($this->GenerateRandomList($this->weather_list));
  }
}

//-- 配役設定の基底クラス --//
class CastConfigBase extends LotteryBuilder{
  //配役テーブル出力
  function OutputCastTable($min = 0, $max = NULL){
    global $ROLE_DATA;

    //設定されている役職名を取得
    $stack = array();
    foreach($this->role_list as $key => $value){
      if($key < $min) continue;
      $stack = array_merge($stack, array_keys($value));
      if($key == $max) break;
    }
    //表示順を決定
    $role_list = array_intersect(array_keys($ROLE_DATA->main_role_list), array_unique($stack));

    $header = '<table class="member">';
    $str = '<tr><th>人口</th>';
    foreach($role_list as $role) $str .= $ROLE_DATA->GenerateMainRoleTag($role, 'th');
    $str .= '</tr>'."\n";
    echo $header . $str;

    //人数毎の配役を表示
    foreach($this->role_list as $key => $value){
      if($key < $min) continue;
      $tag = "<td><strong>{$key}</strong></td>";
      foreach($role_list as $role) $tag .= '<td>' . (int)$value[$role] . '</td>';
      echo '<tr>' . $tag . '</tr>'."\n";
      if($key == $max) break;
      if($key % 20 == 0) echo $str;
    }
    echo '</table>';
  }

  //身代わり君の配役対象外役職リスト取得
  function GetDummyBoyRoleList(){
    global $ROOM;

    $stack = $this->disable_dummy_boy_role_list; //サーバ個別設定を取得
    array_push($stack, 'wolf', 'fox'); //常時対象外の役職を追加
    if($ROOM->IsOption('detective') && ! in_array('detective_common', $stack)){ //探偵村対応
      $stack[] = 'detective_common';
    }
    return $stack;
  }

  //決闘村の配役初期化処理
  function InitializeDuel($user_count){ return true; }

  //決闘村の配役最終処理
  function FinalizeDuel($user_count, &$role_list){ return true; }

  //決闘村の配役処理
  function SetDuel($user_count){
    $role_list = array(); //初期化処理
    $this->InitializeDuel($user_count);

    if(array_sum($this->duel_fix_list) <= $user_count){
      foreach($this->duel_fix_list as $role => $count) $role_list[$role] = $count;
    }
    $rest_user_count = $user_count - array_sum($role_list);
    asort($this->duel_rate_list);
    $total_rate = array_sum($this->duel_rate_list);
    $max_rate_role = array_pop(array_keys($this->duel_rate_list));
    foreach($this->duel_rate_list as $role => $rate){
      if($role == $max_rate_role) continue;
      $role_list[$role] = round($rest_user_count / $total_rate * $rate);
    }
    $role_list[$max_rate_role] = $user_count - array_sum($role_list);

    $this->FinalizeDuel($user_count, $role_list);
    return $role_list;
  }

  //配役フィルタリング処理
  function FilterRoles($user_count, $filter){
    $stack = array();
    foreach($this->role_list[$user_count] as $key => $value){
      $role = 'human';
      foreach($filter as $set_role){
	if(strpos($key, $set_role) !== false){
	  $role = $set_role;
	  break;
	}
      }
      $stack[$role] += (int)$value;
    }
    return $stack;
  }

  //クイズ村の配役処理
  function SetQuiz($user_count){
    $stack = $this->FilterRoles($user_count, array('common', 'wolf', 'mad', 'fox'));
    $stack['human']--;
    $stack['quiz'] = 1;
    return $stack;
  }

  //グレラン村の配役処理
  function SetGrayRandom($user_count){
    return $this->FilterRoles($user_count, array('wolf', 'mad', 'fox'));
  }
}

//-- バージョン情報設定の基底クラス --//
class ScriptInfoBase{
  //TOPページ向けのバージョン情報を出力する
  function Output($full = false){
    global $SERVER_CONF;

    $str = "Powered by {$this->package} {$this->version} from {$this->developer}";
    if($SERVER_CONF->admin) $str .= '<br>Founded by: ' . $SERVER_CONF->admin;
    echo $str;
  }
}
