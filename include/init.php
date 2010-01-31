<?php
//-- 定数を定義 --//
/*
  ServerConfig->site_root を使って CSS や画像等をロードする仕様にすると
  ローカルに保存する場合や、ログを別のサーバに移す場合に手間がかかるので
  JINRO_ROOT で相対パスを定義して共通で使用する仕様に変更しました。
  絶対パスが返る dirname() を使ったパスの定義を行わないで下さい。
*/
if(! defined('JINRO_ROOT')) define('JINRO_ROOT', '.');
define('JINRO_CONF', JINRO_ROOT . '/config');
define('JINRO_INC',  JINRO_ROOT . '/include');
define('JINRO_CSS',  JINRO_ROOT . '/css');
define('JINRO_IMG',  JINRO_ROOT . '/img');

/*
  これ以降の定数は消去予定の loadModule() 用です。
  新しい情報を定義する場合は InitializeConfig() を使ってください。
*/
define('MESSAGE', 'MESSAGE');
define('ROOM_IMG', 'ROOM_IMG');
define('ROLE_IMG', 'ROLE_IMG');
define('ROOM_CONF', 'ROOM_CONF');
define('GAME_CONF', 'GAME_CONF');
define('TIME_CONF', 'TIME_CONF');
define('ICON_CONF', 'ICON_CONF');
define('USER_ICON', 'USER_ICON');
define('ROLES', 'ROLES');
define('CONFIG', 'CONFIG');
define('PLAY_FUNCTIONS', 'PLAY_FUNCTIONS');
define('GAME_FUNCTIONS', 'GAME_FUNCTIONS');
define('GAME_FORMAT_CLASSES', 'GAME_FORMAT_CLASSES');
define('VOTE_FUNCTIONS', 'VOTE_FUNCTIONS');
define('SYSTEM_CLASSES', 'SYSTEM_CLASSES');
define('IMAGE_CLASSES', 'IMAGE_CLASSES');
define('ROLE_CLASSES', 'ROLE_CLASSES');
define('MESSAGE_CLASSES', 'MESSAGE_CLASSES');
define('USER_CLASSES', 'USER_CLASSES');
define('TALK_CLASSES', 'TALK_CLASSES');
define('CHATENGINE_CLASSES', 'CHATENGINE_CLASSES');

//-- デバッグモードのオン/オフ --//
//ServerConfig に移植する予定
#$DEBUG_MODE = false;
$DEBUG_MODE = true;

//-- クラスを定義 --//
class InitializeConfig{
  var $path; //パス情報格納変数
  var $loaded; //ロード情報格納変数

  //依存ファイル情報 (読み込むデータ => 依存するファイル)
  var $depend_file = array(
    'DB_CONF' => 'server_config',
    'SERVER_CONF' => 'server_config',
    'SCRIPT_INFO' => 'version',
    'ROOM_CONF' => 'game_config',
    'GAME_CONF' => 'game_config',
    'CAST_CONF' => 'game_config',
    'TIME_CONF' => 'game_config',
    'ICON_CONF' => 'game_config',
    'USER_ICON' => 'game_config',
    'ROOM_IMG' => 'game_config',
    'ROLE_IMG' => 'game_config',
    'SOUND' => 'game_config',
    'MESSAGE' => 'message',
    'GAME_OPT_MESS' => 'message',
    'VICT_MESS' => 'message',
    'VOTE_MESS' => 'message',
    'ROLES' => 'role_manager_class',
    'TIME_CALC' => 'time_calc',
    'server_config' => 'system_class',
    'game_config' => 'system_class',
    'game_vote_functions' => 'game_functions',
    'game_play_functions' => 'user_class',
    'game_functions' => 'system_class',
    'system_class' => 'functions',
    'user_class' => 'game_functions',
    'role_manager_class' => 'role_class',
    'role_class' => 'game_format'
  );

  //依存クラス情報 (読み込むデータ => 依存するクラス)
  var $depend_class = array(
    'GAME_OPT_CAPT' => 'GAME_OPT_MESS',
    'TIME_CALC' => array('TIME_CONF', 'ROOM_CONF'),
    'game_play_functions' => 'ROLE_IMG',
    'user_class' => array('GAME_CONF', 'MESSAGE')
  );

  //クラス名情報 (グローバル変数名 => 読み込むクラス)
  var $class_list = array(
    'DB_CONF'=> 'DatabaseConfig',
    'SERVER_CONF'=> 'ServerConfig',
    'SCRIPT_INFO'=> 'ScriptInfo',
    'ROOM_CONF'=> 'RoomConfig',
    'GAME_CONF'=> 'GameConfig',
    'CAST_CONF'=> 'CastConfig',
    'TIME_CONF'=> 'TimeConfig',
    'ICON_CONF'=> 'IconConfig',
    'USER_ICON'=> 'UserIcon',
    'ROOM_IMG'=> 'RoomImage',
    'ROLE_IMG'=> 'RoleImage',
    'SOUND'=> 'Sound',
    'COOKIE'=> 'CookieDataSet',
    'MESSAGE'=> 'Message',
    'GAME_OPT_MESS'=> 'GameOptionMessage',
    'GAME_OPT_CAPT'=> 'GameOptionCaptionMessage',
    'VICT_MESS'=> 'VictoryMessage',
    'VOTE_MESS'=> 'VoteMessage',
    'ROLES'=> 'Roles',
    'TIME_CALC'=> 'TimeCalculation'
  );

  function InitializeConfig(){ $this->__construct(); }

  function __construct(){
    $this->path->root    = JINRO_ROOT;
    $this->path->config  = JINRO_CONF;
    $this->path->include = JINRO_INC;
    $this->loaded->file  = array();
    $this->loaded->class = array();
  }

  //依存解決処理関数
  function LoadDependence($name){
    $depend_file = $this->depend_file[$name];
    if(! is_null($depend_file)) $this->LoadFile($depend_file);

    $depend_class = $this->depend_class[$name];
    if(! is_null($depend_class)) $this->LoadClass($depend_class);
  }

  //ファイルロード関数
  function LoadFile($name){
    $name_list = func_get_args();
    if(is_array($name_list[0])) $name_list = $name_list[0];
    if(count($name_list) > 1){
      foreach($name_list as $name) $this->LoadFile($name);
      return;
    }

    if(is_null($name)) return false;
    if(in_array($name, $this->loaded->file)) return false;
    $this->LoadDependence($name);

    switch($name){
    case 'server_config':
    case 'game_config':
    case 'message':
    case 'version':
      $path = $this->path->config;
      break;

    case 'paparazzi':
      $path = $this->path->root;
      break;

    case 'mb-emulator':
      $path = $this->path->root . '/module';
      break;

    case 'role_manager_class':
    case 'role_class':
      $path = $this->path->include . '/role';
      break;

    case 'chatengine':
      $path = $this->path->include . '/' . $name;
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
    if(is_array($name_list[0])) $name_list = $name_list[0];
    if(count($name_list) > 1){
      foreach($name_list as $name) $this->LoadClass($name);
      return;
    }

    if(is_null($name)) return false;
    if(in_array($name, $this->loaded->class)) return false;
    $this->LoadDependence($name);

    if(is_null($class_name = $this->class_list[$name])) return false;
    $GLOBALS[$name] =& new $class_name();
    $this->loaded->class[] = $name;
    return true;
  }
}

//-- 初期化処理 --//
$INIT_CONF =& new InitializeConfig();
$INIT_CONF->LoadFile('paparazzi', 'request_class');

//mbstring非対応の場合、エミュレータを使用する
if(! extension_loaded('mbstring')) $INIT_CONF->LoadFile('mb-emulator');

$INIT_CONF->LoadClass('DB_CONF', 'SERVER_CONF');

#PrintData($INIT_CONF);

//-- スクリプト群の文字コード --//
//変更する場合は全てのファイル自体の文字コードを自前で変更してください

//エンコーディング指定 PHPバージョンによって指定方法が異なる
$php_version_array = explode('.', phpversion());
if($php_version_array[0] <= 4 && $php_version_array[1] < 3){ //4.3.x未満
  //encoding $SERVER_CONF->encode;  //エラーが出る？
}
else{ //4.3.x以降
  declare(encoding='EUC-JP'); //変数を入れるとパースエラーが返るのでハードコード
}

//-- マルチバイト入出力指定 --//
if(extension_loaded('mbstring')){
  mb_language('ja');
  mb_internal_encoding($SERVER_CONF->encode);
  mb_http_input('auto');
  mb_http_output($SERVER_CONF->encode);
}

//-- 海外のサーバでも動くようにヘッダ強制指定 --//
//海外サーバ等で文字化けする場合に指定します
/*
if(! headers_sent()){ //ヘッダがまだ何も送信されていない場合送信する
  header("Content-type: text/html; charset={$SERVER_CONF->encode}");
  header('Content-Language: ja');
}
*/

//-- 関数定義 --//
/*
  この関数は消去する予定です
  新しい情報を定義する場合は InitializeConfig() を使ってください
*/
function loadModule($name) {
  #print_r($GLOBALS); echo "\n $name \n";
  if(func_num_args() == 1){
    if(! empty($GLOBALS[$name])) return true;
    if(function_exists('shot')) shot("loading $name...", 'loadModule');
    switch($name){
    case MESSAGE:
      if(loadModule(MESSAGE_CLASSES)){ //システムメッセージ格納クラス
        $GLOBALS[$name] = new Message();
        return true;
      }
      return false;
    case ROOM_IMG:
      if(loadModule(SYSTEM_CLASSES)){
        $GLOBALS[$name] = new RoomImage();
        return true;
      }
      return false;
    case ROLE_IMG:
      if(loadModule(SYSTEM_CLASSES)){
        $GLOBALS[$name] = new RoleImage();
        return true;
      }
      return false;
    case ROOM_CONF:
      if(loadModule(CONFIG)){
        $GLOBALS[$name] = new RoomConfig();
        return true;
      }
      return false;
    case GAME_CONF:
      if(loadModule(CONFIG)){
        $GLOBALS[$name] = new GameConfig();
        return true;
      }
      return false;
    case TIME_CONF:
      if(loadModule(CONFIG)){
        $GLOBALS[$name] = new TimeConfig();
        return true;
      }
      return false;
    case ICON_CONF:
      if(loadModule(CONFIG)){
        $GLOBALS[$name] = new IconConfig();
        return true;
      }
      return false;
    case USER_ICON:
      if(loadModule(CONFIG)){
        $GLOBALS[$name] = new UserIcon();
        return true;
      }
      return false;
    case ROLES:
      if(loadModule(ROLE_CLASSES)){
        $GLOBALS[$name] = new Roles();
        return true;
      }
      return false;
    case MESSAGE_CLASSES:
      return $GLOBALS[$name] = include(JINRO_INC . '/message_class.php');
    case ROLE_CLASSES:
      if(loadModule(GAME_FORMAT_CLASSES)){
        return $GLOBALS[$name] = include(JINRO_INC . '/role/role_manager_class.php');
      }
    case SYSTEM_CLASSES:
      return $GLOBALS[$name] = include(JINRO_INC . '/system_class.php');  //システム情報格納クラス
    case CONFIG:
      return $GLOBALS[$name] = include(JINRO_INC . '/config.php');          //高度な設定
    case FUNCTIONS:
      return $GLOBALS[$name] = include(JINRO_INC . '/functions.php');          //高度な設定
    case PLAY_FUNCTIONS:
      return $GLOBALS[$name] = include(JINRO_INC . '/game_play_functions.php');
    case GAME_FUNCTIONS:
      return $GLOBALS[$name] = include(JINRO_INC . '/game_functions.php');
    case GAME_FORMAT_CLASSES:
      return $GLOBALS[$name] = include(JINRO_INC . '/game_format.php');
    case VOTE_FUNCTIONS:
      if(loadModule(GAME_FUNCTIONS)){
	return $GLOBALS[$name] = include(JINRO_INC . '/game_vote_functions.php');
      }
    case USER_CLASSES:
      return $GLOBALS[$name] = include(JINRO_INC . '/user_class.php');
    case TALK_CLASSES:
      return $GLOBALS[$name] = include(JINRO_INC . '/talk_class.php');
    case CHATENGINE_CLASSES:
      return $GLOBALS[$name] = include(JINRO_INC . '/chatengine/chatengine.php');
    }
    return false;
  }
  else {
    $name_list = func_get_args();
    foreach($name_list as $name){
      $result[$name] = loadModule($name);
    }
    return $result;
  }
}
?>