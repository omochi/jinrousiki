<?php
if(! defined('JINRO_ROOT')) define('JINRO_ROOT', '.');
//define('JINRO_ROOT', dirname(dirname(__FILE__))); css や画像パスの処理の都合で絶対パスを使用しない
define('JINRO_INC', JINRO_ROOT.'/include');
define('JINRO_CSS', JINRO_ROOT.'/css');

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

//デバッグモードのオン/オフ
$DEBUG_MODE = false;
#$DEBUG_MODE = true;
$INIT_CONF =& new InitializeConfig();

$INIT_CONF->LoadFile('paparazzi', 'request_class', 'functions');

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
//ヘッダがまだ何も送信されていない場合送信する
if(! headers_sent()){
  header("Content-type: text/html; charset={$SERVER_CONF->encode}");
  header('Content-Language: ja');
}

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


class InitializeConfig{
  var $path;
  var $loaded;
  var $depend_file = array(
    'DB_CONF' => 'setting',
    'SERVER_CONF' => 'setting',
    'SCRIPT_INFO' => 'version',
    'ROOM_CONF' => 'config',
    'GAME_CONF' => 'config',
    'TIME_CONF' => 'config',
    'ICON_CONF' => 'config',
    'USER_ICON' => 'config',
    'ROOM_IMG' => 'system_class',
    'ROLE_IMG' => 'system_class',
    'MESSAGE' => 'message_class',
    'ROLES' => 'role/role_manager_class',
    'TIME_CALC' => 'time_calc',
    'game_vote_functions' => 'game_functions',
    'game_play_functions' => 'user_class',
    'game_functions' => 'system_class',
    'user_class' => 'game_functions',
    'role/role_manager_class' => 'role/role_class',
    'role/role_class' => 'game_format'
  );

  var $depend_class = array(
    'TIME_CALC' => array('TIME_CONF', 'ROOM_CONF'),
    'game_play_functions' => 'ROLE_IMG',
    'user_class' => array('GAME_CONF', 'MESSAGE')
  );

  function InitializeConfig(){
    $this->path->root = JINRO_ROOT;
    $this->path->include = JINRO_INC;
    $this->loaded->file  = array();
    $this->loaded->class = array();
  }

  function LoadDependence($name){
    $depend_file = $this->depend_file[$name];
    if(! is_null($depend_file)) $this->LoadFile($depend_file);

    $depend_class = $this->depend_class[$name];
    if(! is_null($depend_class)) $this->LoadClass($depend_class);
  }

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
    case 'paparazzi':
      $path = $this->path->root;
      break;

    case 'mb-emulator':
      $path = $this->path->root . '/module';
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

    switch($name){
    case 'DB_CONF':
      $GLOBALS[$name] = new DatabaseConfig();
      break;

    case 'SERVER_CONF':
      $GLOBALS[$name] = new ServerConfig();
      break;

    case 'SCRIPT_INFO':
      $GLOBALS[$name] = new ScriptInformation();
      break;

    case 'ROOM_CONF':
      $GLOBALS[$name] = new RoomConfig();
      break;

    case 'GAME_CONF':
      $GLOBALS[$name] = new GameConfig();
      break;

    case 'TIME_CONF':
      $GLOBALS[$name] = new TimeConfig();
      break;

    case 'ICON_CONF':
      $GLOBALS[$name] = new IconConfig();
      break;

    case 'USER_ICON':
      $GLOBALS[$name] = new UserIcon();
      break;

    case 'ROOM_IMG':
      $GLOBALS[$name] = new RoomImage();
      break;

    case 'ROLE_IMG':
      $GLOBALS[$name] = new RoleImage();
      break;

    case 'MESSAGE':
      $GLOBALS[$name] = new Message();
      break;

    case 'ROLES':
      $GLOBALS[$name] = new Roles();
      break;

    case 'TIME_CALC':
      $GLOBALS[$name] = new TimeCalculation();
      break;
    }
    $this->loaded->class[] = $name;
    return true;
  }
}
?>