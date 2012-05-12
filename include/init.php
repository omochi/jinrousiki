<?php
//error_reporting(E_ALL);
//-- 定数を定義 --//
/*
  ServerConfig->site_root を使って CSS や画像等をロードする仕様にすると
  ローカルに保存する場合や、ログを別のサーバに移す場合に手間がかかるので
  JINRO_ROOT で相対パスを定義して共通で使用する仕様に変更しました。
  絶対パスが返る dirname() を使ったパスの定義を行わないで下さい。
*/
if (! defined('JINRO_ROOT')) define('JINRO_ROOT', '.');
define('JINRO_CONF', JINRO_ROOT . '/config');
define('JINRO_INC',  JINRO_ROOT . '/include');
define('JINRO_CSS',  JINRO_ROOT . '/css');
define('JINRO_IMG',  JINRO_ROOT . '/img');
define('JINRO_MOD',  JINRO_ROOT . '/module');

//-- クラスを定義 --//
/*
  初期化の読み込みを最適化するのが目的なので、依存情報に
  確実に読み込まれているデータを入れる必要はない。
  逆にコード上必須ではないが常にセットで使われるデータを入れると良い。
*/
class InitializeConfig {
  public $path; //パス情報格納変数
  public $loaded; //ロード情報格納変数

  //依存ファイル情報 (読み込むデータ => 依存するファイル)
  public $depend_file = array(
    'GAME_OPT_CONF'        => 'game_option_config',
    'SRC_UP_CONF'          => 'src_upload_config',
    'ROOM_IMG'             => 'system_class',
    'ROLE_IMG'             => 'system_class',
    'ROOM_OPT'             => array('room_config', 'time_config', 'option/room_option_class',
				    'option/room_option_item_class'),
    'MESSAGE'              => 'message',
    'GAME_OPT_MESS'        => 'message',
    'WINNER_MESS'          => 'message',
    'VOTE_MESS'            => 'message',
    'ROLES'                => 'role_class',
    'TIME_CALC'            => array('time_config', 'room_config', 'game_config', 'cast_config',
				    'info_functions'),
    'TWITTER'              => array('twitter_config', 'twitter'),
    'PAPARAZZI'            => 'paparazzi_class',
    'server_config'        => array('system_class', 'functions'), //常時ロードされる
    'chaos_config'         => 'cast_config',
    'shared_server_config' => 'info_functions',
    'copyright_config'     => array('version', 'info_functions'),
    'database_class'       => 'database_config',
    'system_class'         => 'room_class', //常時ロードされる
    'room_class'           => 'option_class',
    'user_class'           => array('game_config', 'game_functions'),
    'talk_class'           => 'user_class',
    'role_class'           => 'game_format',
    'icon_class'           => 'icon_config',
    'user_icon_class'      => 'user_icon_config',
    'sound_class'          => 'sound_config',
    'login_class'          => 'session_class',
    'game_view_class'      => array('icon_class', 'talk_class'),
    'game_log_class'       => array('session_class', 'talk_class'),
    'user_manager_class'   => array('room_config', 'game_config', 'room_class', 'user_class',
				    'session_class', 'icon_functions'),
    'icon_view_class'      => 'icon_functions',
    'icon_edit_class'      => 'icon_functions',
    'icon_upload_class'    => array('session_class', 'icon_functions'),
    'paparazzi_class'      => 'paparazzi',
    'index_functions'      => array('menu_config', 'bbs_config', 'version'),
    'game_play_functions'  => 'user_class',
    'game_vote_functions'  => 'game_functions',
    'icon_functions'       => array('icon_class', 'user_icon_class'),
    'oldlog_functions'     => array('oldlog_config', 'cast_config'),
    'setup_class'          => array('setup_config', 'version', 'database_class'),
    'test/objection'       => array('game_config', 'sound_class')
  );

  //依存クラス情報 (読み込むデータ => 依存するクラス)
  public $depend_class = array(
    'ROOM_OPT'            => 'GAME_OPT_CONF',
    'GAME_OPT_CAPT'       => 'GAME_OPT_MESS',
    'TIME_CALC'           => array('ROOM_IMG', 'ROLE_DATA'),
    'user_class'          => array('ROLE_DATA', 'MESSAGE'),
    'game_view_class'     => array('ROLES'),
    'game_log_class'      => 'ROLES',
    'user_manager_class'  => 'MESSAGE',
    'game_play_functions' => 'ROLE_IMG',
    'oldlog_functions'    => array('ROOM_IMG', 'ROOM_OPT', 'GAME_OPT_MESS'),
  );

  //クラス名情報 (グローバル変数名 => 読み込むクラス)
  public $class_list = array(
    'GAME_OPT_CONF' => 'GameOptionConfig',
    'SRC_UP_CONF'   => 'SourceUploadConfig',
    'TWITTER'       => 'TwitterConfig',
    'ROOM_IMG'      => 'RoomImage',
    'ROLE_IMG'      => 'RoleImage',
    'ROOM_OPT'      => 'RoomOption',
    'MESSAGE'       => 'Message',
    'GAME_OPT_MESS' => 'GameOptionMessage',
    'GAME_OPT_CAPT' => 'GameOptionCaptionMessage',
    'WINNER_MESS'   => 'WinnerMessage',
    'VOTE_MESS'     => 'VoteMessage',
    'COOKIE'        => 'CookieDataSet',
    'ROLE_DATA'     => 'RoleData',
    'ROLES'         => 'RoleManager',
    'TIME_CALC'     => 'TimeCalculation',
    'PAPARAZZI'     => 'Paparazzi'
  );

  function __construct(){
    $this->path = new StdClass();
    $this->path->root    = JINRO_ROOT;
    $this->path->config  = JINRO_CONF;
    $this->path->include = JINRO_INC;
    $this->path->module  = JINRO_MOD;
    $this->loaded = new StdClass();
    $this->loaded->file  = array();
    $this->loaded->class = array();

    $this->LoadFile('database_class', 'server_config');
  }

  //依存情報設定
  protected function SetDepend($type, $name, $depend){
    if (is_null($this->$type)) return false;
    $this->{$type}[$name] = $depend;
    return true;
  }

  //依存クラス情報設定 ＆ ロード
  protected function SetClass($name, $class){
    if (! $this->SetDepend('class_list', $name, $class)) return false;
    $this->LoadClass($name);
    return true;
  }

  //依存解決処理
  protected function LoadDependence($name){
    if (array_key_exists($name, $this->depend_file))  $this->LoadFile($this->depend_file[$name]);
    if (array_key_exists($name, $this->depend_class)) $this->LoadClass($this->depend_class[$name]);
  }

  //ファイルロード
  function LoadFile($name){
    $name_list = func_get_args();
    if (is_array($name_list[0])) $name_list = $name_list[0];
    if (count($name_list) > 1) {
      foreach ($name_list as $name) $this->LoadFile($name);
      return;
    }

    if (is_null($name) || in_array($name, $this->loaded->file)) return false;
    $this->LoadDependence($name);

    switch ($name) {
    case 'copyright_config':
    case 'version':
      $path = $this->path->config . '/system';
      break;

    case 'game_config':
    case 'cast_config':
    case 'chaos_config':
    case 'message':
    case 'time_config':
    case 'icon_config':
    case 'sound_config':
      $path = $this->path->config . '/game';
      break;

    case 'database_config':
    case 'server_config':
    case 'room_config':
    case 'game_option_config':
    case 'user_icon_config':
    case 'menu_config':
    case 'bbs_config':
    case 'oldlog_config':
    case 'shared_server_config':
    case 'src_upload_config':
    case 'twitter_config':
    case 'setup_config':
      $path = $this->path->config . '/server';
      break;

    case 'mb-emulator':
    case 'twitter':
      $path = $this->path->module . '/' . $name;
      break;

    case 'option_class':
    case 'role_class':
    case 'chatengine':
    case 'feedengine':
    case 'paparazzi':
    case 'paparazzi_class':
      $path = $this->path->include . '/' . @array_shift(explode('_', $name));
      break;

    default:
      $path = $this->path->include;
      break;
    }

    require_once($path . '/' . $name . '.php');
    $this->loaded->file[] = $name;
    return true;
  }

  function LoadClass($name){
    $name_list = func_get_args();
    if (is_array($name_list[0])) $name_list = $name_list[0];
    if (count($name_list) > 1) {
      foreach ($name_list as $name) $this->LoadClass($name);
      return;
    }

    if (is_null($name) || in_array($name, $this->loaded->class)) return false;
    $this->LoadDependence($name);

    if (is_null($class_name = $this->class_list[$name])) return false;
    $GLOBALS[$name] = new $class_name();
    $this->loaded->class[] = $name;
    return true;
  }

  function LoadRequest($class = null, $load = false){
    if ($load) $this->LoadFile('game_config');
    $this->LoadFile('request_class');
    return RQ::Load($class);
  }
}

//-- 初期化処理 --//
$INIT_CONF = new InitializeConfig();

//mbstring 非対応の場合、エミュレータを使用する
if (! extension_loaded('mbstring')) $INIT_CONF->LoadFile('mb-emulator');

if (Security::CheckValue($_REQUEST) || Security::CheckValue($_SERVER)) die;

//デバッグ用ツールをロード
ServerConfig::$debug_mode ? $INIT_CONF->LoadClass('PAPARAZZI') : $INIT_CONF->LoadFile('paparazzi');

//PrintData($INIT_CONF); //テスト用

//-- スクリプト群の文字コード --//
/*
  変更する場合は全てのファイル自体の文字コードを自前で変更してください
  declare encoding は --enable-zend-multibyte が有効な PHP でのみ機能します
*/
//declare(encoding='UTF-8');

//-- マルチバイト入出力指定 --//
if (extension_loaded('mbstring')) {
  mb_language('ja');
  mb_internal_encoding(ServerConfig::$encode);
  mb_http_input('auto');
  mb_http_output(ServerConfig::$encode);
}

//-- ヘッダ強制指定 --//
if (ServerConfig::$set_header_encode && ! headers_sent()) { //ヘッダ未送信時にセットする
  header(sprintf('Content-type: text/html; charset=%s', ServerConfig::$encode));
  header('Content-Language: ja');
}
