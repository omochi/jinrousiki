<?php
//-- �������� --//
/*
  ServerConfig->site_root ��Ȥä� CSS �����������ɤ�����ͤˤ����
  ���������¸������䡢�����̤Υ����Ф˰ܤ����˼�֤�������Τ�
  JINRO_ROOT �����Хѥ���������ƶ��̤ǻ��Ѥ�����ͤ��ѹ����ޤ�����
  ���Хѥ����֤� dirname() ��Ȥä��ѥ��������Ԥ�ʤ��ǲ�������
*/
if(! defined('JINRO_ROOT')) define('JINRO_ROOT', '.');
define('JINRO_CONF', JINRO_ROOT . '/config');
define('JINRO_INC',  JINRO_ROOT . '/include');
define('JINRO_CSS',  JINRO_ROOT . '/css');
define('JINRO_IMG',  JINRO_ROOT . '/img');

/*
  ����ʹߤ�����Ͼõ�ͽ��� loadModule() �ѤǤ���
  ��������������������� InitializeConfig() ��ȤäƤ���������
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

//-- �ǥХå��⡼�ɤΥ���/���� --//
//ServerConfig �˰ܿ�����ͽ��
#$DEBUG_MODE = false;
$DEBUG_MODE = true;

//-- ���饹����� --//
class InitializeConfig{
  var $path; //�ѥ������Ǽ�ѿ�
  var $loaded; //���ɾ����Ǽ�ѿ�

  //��¸�ե�������� (�ɤ߹���ǡ��� => ��¸����ե�����)
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

  //��¸���饹���� (�ɤ߹���ǡ��� => ��¸���륯�饹)
  var $depend_class = array(
    'GAME_OPT_CAPT' => 'GAME_OPT_MESS',
    'TIME_CALC' => array('TIME_CONF', 'ROOM_CONF'),
    'game_play_functions' => 'ROLE_IMG',
    'user_class' => array('GAME_CONF', 'MESSAGE')
  );

  //���饹̾���� (�����Х��ѿ�̾ => �ɤ߹��९�饹)
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

  //��¸�������ؿ�
  function LoadDependence($name){
    $depend_file = $this->depend_file[$name];
    if(! is_null($depend_file)) $this->LoadFile($depend_file);

    $depend_class = $this->depend_class[$name];
    if(! is_null($depend_class)) $this->LoadClass($depend_class);
  }

  //�ե�������ɴؿ�
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

//-- ��������� --//
$INIT_CONF =& new InitializeConfig();
$INIT_CONF->LoadFile('paparazzi', 'request_class');

//mbstring���б��ξ�硢���ߥ�졼������Ѥ���
if(! extension_loaded('mbstring')) $INIT_CONF->LoadFile('mb-emulator');

$INIT_CONF->LoadClass('DB_CONF', 'SERVER_CONF');

#PrintData($INIT_CONF);

//-- ������ץȷ���ʸ�������� --//
//�ѹ�����������ƤΥե����뼫�Τ�ʸ�������ɤ������ѹ����Ƥ�������

//���󥳡��ǥ��󥰻��� PHP�С������ˤ�äƻ�����ˡ���ۤʤ�
$php_version_array = explode('.', phpversion());
if($php_version_array[0] <= 4 && $php_version_array[1] < 3){ //4.3.x̤��
  //encoding $SERVER_CONF->encode;  //���顼���Ф롩
}
else{ //4.3.x�ʹ�
  declare(encoding='EUC-JP'); //�ѿ��������ȥѡ������顼���֤�Τǥϡ��ɥ�����
}

//-- �ޥ���Х��������ϻ��� --//
if(extension_loaded('mbstring')){
  mb_language('ja');
  mb_internal_encoding($SERVER_CONF->encode);
  mb_http_input('auto');
  mb_http_output($SERVER_CONF->encode);
}

//-- �����Υ����ФǤ�ư���褦�˥إå��������� --//
//��������������ʸ������������˻��ꤷ�ޤ�
/*
if(! headers_sent()){ //�إå����ޤ�������������Ƥ��ʤ������������
  header("Content-type: text/html; charset={$SERVER_CONF->encode}");
  header('Content-Language: ja');
}
*/

//-- �ؿ���� --//
/*
  ���δؿ��Ͼõ��ͽ��Ǥ�
  ��������������������� InitializeConfig() ��ȤäƤ�������
*/
function loadModule($name) {
  #print_r($GLOBALS); echo "\n $name \n";
  if(func_num_args() == 1){
    if(! empty($GLOBALS[$name])) return true;
    if(function_exists('shot')) shot("loading $name...", 'loadModule');
    switch($name){
    case MESSAGE:
      if(loadModule(MESSAGE_CLASSES)){ //�����ƥ��å�������Ǽ���饹
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
      return $GLOBALS[$name] = include(JINRO_INC . '/system_class.php');  //�����ƥ�����Ǽ���饹
    case CONFIG:
      return $GLOBALS[$name] = include(JINRO_INC . '/config.php');          //���٤�����
    case FUNCTIONS:
      return $GLOBALS[$name] = include(JINRO_INC . '/functions.php');          //���٤�����
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