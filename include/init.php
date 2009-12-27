<?php
define('JINRO_ROOT', dirname(dirname(__FILE__)));
define('JINRO_INC', JINRO_ROOT.'/include');

//デバッグモードのオン/オフ
#$DEBUG_MODE = false;
$DEBUG_MODE = true;
require_once(JINRO_ROOT . '/paparazzi.php');    //デバッグ用
require_once(JINRO_INC . '/request_class.php');
require_once(JINRO_INC . '/test_initiator.php');

//mbstring非対応の場合、エミュレータを使用する
if(! extension_loaded('mbstring')){
  require_once(JINRO_ROOT . '/module/mb-emulator.php');
}
require_once(JINRO_INC . '/setting.php');
$DB_CONF = new DatabaseConfig();
$SERVER_CONF = new ServerConfig();

require_once(JINRO_INC . '/version.php');         //バージョン情報
require_once(JINRO_INC . '/contenttype_set.php'); //ヘッダの文字コード設定
//ヘルパ関数
require_once(JINRO_INC . '/functions.php');

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

function loadModule($name) {
  #print_r($GLOBALS); echo "\n $name \n";
  if (func_num_args() == 1){
    if (!empty($GLOBALS[$name]))
      return true;
    shot("loading $name...", 'loadModule');
    switch($name){
    case MESSAGE:
      if (loadModule(MESSAGE_CLASSES)) { //システムメッセージ格納クラス
        $GLOBALS[$name] = new Message();
        return true;
      }
      return false;
    case ROOM_IMG:
      if (loadModule(SYSTEM_CLASSES)) {
        $GLOBALS[$name] = new RoomImage();
        return true;
      }
      return false;
    case ROLE_IMG:
      if (loadModule(SYSTEM_CLASSES)) {
        $GLOBALS[$name] = new RoleImage();
        return true;
      }
      return false;
    case ROOM_CONF:
      if (loadModule(CONFIG)) {
        $GLOBALS[$name] = new RoomConfig();
        return true;
      }
      return false;
    case GAME_CONF:
      if (loadModule(CONFIG)) {
        $GLOBALS[$name] = new GameConfig();
        return true;
      }
      return false;
    case TIME_CONF:
      if (loadModule(CONFIG)) {
        $GLOBALS[$name] = new TimeConfig();
        return true;
      }
      return false;
    case ICON_CONF:
      if (loadModule(CONFIG)) {
        $GLOBALS[$name] = new IconConfig();
        return true;
      }
      return false;
    case USER_ICON:
      if (loadModule(CONFIG)) {
        $GLOBALS[$name] = new UserIcon();
        return true;
      }
      return false;
    case ROLES:
      if (loadModule(ROLE_CLASSES)) {
        $GLOBALS[$name] = new Roles();
        return true;
      }
      return false;
    case MESSAGE_CLASSES:
      return $GLOBALS[$name] = include(JINRO_INC . '/message_class.php');
    case ROLE_CLASSES:
      if (loadModule(GAME_FORMAT_CLASSES)) {
        return $GLOBALS[$name] = include(JINRO_INC . '/role/role_manager_class.php');
      }
    case SYSTEM_CLASSES:
      return $GLOBALS[$name] = include(JINRO_INC . '/system_class.php');  //システム情報格納クラス
    case CONFIG:
      return $GLOBALS[$name] = include(JINRO_INC . '/config.php');          //高度な設定
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
    foreach($name_list as $name) {
      $result[$name] = loadModule($name);
    }
    return $result;
  }
}
?>