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
define('JINRO_MOD',  JINRO_ROOT . '/module');

//-- �ǥХå��⡼�ɤΥ���/���� --//
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
    'USER_ICON' => 'server_config',
    'SCRIPT_INFO' => 'version',
    'SESSION' => 'system_class',
    'ROOM_CONF' => 'game_config',
    'GAME_CONF' => 'game_config',
    'CAST_CONF' => 'game_config',
    'TIME_CONF' => 'game_config',
    'ICON_CONF' => 'game_config',
    'ROOM_IMG' => 'game_config',
    'ROLE_IMG' => 'game_config',
    'SOUND' => 'game_config',
    'MESSAGE' => 'message',
    'GAME_OPT_MESS' => 'message',
    'VICT_MESS' => 'message',
    'VOTE_MESS' => 'message',
    'RQ_ARGS' => 'request_class',
    'ROLES' => 'role_class',
    'TIME_CALC' => 'time_calc',
    'PAPARAZZI' => 'paparazzi_class',
    'server_config' => 'system_class',
    'game_config' => 'system_class',
    'game_vote_functions' => 'game_functions',
    'game_play_functions' => 'user_class',
    'game_functions' => 'system_class',
    'system_class' => array('functions', 'room_class'),
    'icon_functions' => 'system_class',
    'room_class' => 'option_class',
    'user_class' => 'game_functions',
    'role_class' => 'game_format'
  );

  //��¸���饹���� (�ɤ߹���ǡ��� => ��¸���륯�饹)
  var $depend_class = array(
    'GAME_OPT_CAPT' => 'GAME_OPT_MESS',
    'TIME_CALC' => array('TIME_CONF', 'ROOM_CONF'),
    'game_play_functions' => 'ROLE_IMG',
    'user_class' => array('GAME_CONF', 'MESSAGE'),
    'icon_functions' => array('ICON_CONF', 'USER_ICON')
  );

  //���饹̾���� (�����Х��ѿ�̾ => �ɤ߹��९�饹)
  var $class_list = array(
    'DB_CONF' => 'DatabaseConfig',
    'SERVER_CONF' => 'ServerConfig',
    'SCRIPT_INFO' => 'ScriptInfo',
    'SESSION' => 'Session',
    'ROOM_CONF' => 'RoomConfig',
    'GAME_CONF' => 'GameConfig',
    'CAST_CONF' => 'CastConfig',
    'TIME_CONF' => 'TimeConfig',
    'ICON_CONF' => 'IconConfig',
    'USER_ICON' => 'UserIcon',
    'ROOM_IMG' => 'RoomImage',
    'ROLE_IMG' => 'RoleImage',
    'SOUND' => 'Sound',
    'COOKIE' => 'CookieDataSet',
    'MESSAGE' => 'Message',
    'GAME_OPT_MESS' => 'GameOptionMessage',
    'GAME_OPT_CAPT' => 'GameOptionCaptionMessage',
    'VICT_MESS' => 'VictoryMessage',
    'VOTE_MESS' => 'VoteMessage',
    'RQ_ARGS' => 'RequestBase',
    'ROLES' => 'RoleManager',
    'TIME_CALC' => 'TimeCalculation',
    'PAPARAZZI' => 'Paparazzi'
  );

  function InitializeConfig(){ $this->__construct(); }
  function __construct(){
    $this->path->root    = JINRO_ROOT;
    $this->path->config  = JINRO_CONF;
    $this->path->include = JINRO_INC;
    $this->path->module  = JINRO_MOD;
    $this->loaded->file  = array();
    $this->loaded->class = array();
  }

  //��¸��������
  function SetDepend($type, $name, $depend){
    if(is_null($this->$type)) return false;
    $this->{$type}[$name] = $depend;
    return true;
  }

  //��¸���饹�������� �� ����
  function SetClass($name, $class){
    if(! $this->SetDepend('class_list', $name, $class)) return false;
    $this->LoadClass($name);
    return true;
  }

  //��¸������
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

    if(is_null($name) || in_array($name, $this->loaded->file)) return false;
    $this->LoadDependence($name);

    switch($name){
    case 'server_config':
    case 'game_config':
    case 'message':
    case 'version':
      $path = $this->path->config;
      break;

    case 'mb-emulator':
      $path = $this->path->module . '/' . $name;
      break;

    case 'chatengine':
    case 'paparazzi_class':
    case 'role_class':
      $path = $this->path->include . '/' . array_shift(explode('_', $name));
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

    if(is_null($name) || in_array($name, $this->loaded->class)) return false;
    $this->LoadDependence($name);

    if(is_null($class_name = $this->class_list[$name])) return false;
    $GLOBALS[$name] =& new $class_name();
    $this->loaded->class[] = $name;
    return true;
  }

  function LoadRequest($class = NULL){
    return $this->SetClass('RQ_ARGS', $class);
  }
}

//-- ��������� --//
require_once(JINRO_INC . '/paparazzi/paparazzi.php');

$INIT_CONF =& new InitializeConfig();
if($DEBUG_MODE) $INIT_CONF->LoadClass('PAPARAZZI');

//mbstring ���б��ξ�硢���ߥ�졼������Ѥ���
if(! extension_loaded('mbstring')) $INIT_CONF->LoadFile('mb-emulator');

$INIT_CONF->LoadClass('DB_CONF', 'SERVER_CONF');

//PrintData($INIT_CONF); //�ƥ�����

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
