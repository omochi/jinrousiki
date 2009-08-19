<?php
require_once(dirname(__FILE__).'/role_class.php');

class Roles{
  var $class = array();

  function Roles(){
    $this->LoadRoles();
  }

  function __construct(){
    $this->LoadRoles();
  }

  function LoadRoles(){
    $dir = opendir(dirname(__FILE__));
    while(($file = readdir($dir)) !== false){
      shot(sprintf("�ե�����ΰ���:{$file}(��ĥ�� %s)", substr($file, -4, 4)));
      if(substr($file, -4, 4) == '.php'){
        $this->AddRoleFrom($file);
      }
    }
    closedir($dir);
  }

  function AddRoleFrom($file_name){
    shot("{$file_name}���饯�饹����Ф��ޤ���");
    if(preg_match('/.*_class.php$/i', $file_name)){
      shot(">>>����:���ΤΥե�����Ǥ���");
      return false;
    }
    include_once($file_name);
    $role = substr($file_name, 0, -4);
    $class_name = "Role_{$role}";
    $this->class[$role] = $class_name;
    shot(">>>����({$class_name})");
  }

  function Instantiate($role_name, $user = NULL){
    global $USERS, $SELF;
    shot("{$role_name}�ο��������󥹥��󥹤��������ޤ���");
    #$USERS->Load();
    return new $this->class[$role_name](isset($user) ? $user : $SELF);
  }

  function GetWhisperingUserInfo($role_name, &$class_attr){
    if(strpos($role_name, 'common') !== false){
      $class_attr = 'talk-common';
      return '��ͭ�Ԥξ���';
    }
    elseif(strpos($role_name, 'wolf') !== false){
      return 'ϵ�α��ʤ�';
    }
    return false;
  }

  function GetWhisperingSound($role_name, $talk, &$class_attr){
    global $MESSAGE;
    switch ($role_name){
    case 'common':
      $class_attr = 'say-common';
      return $MESSAGE->common_talk;
    case 'wolf':
      return $MESSAGE->wolf_howl;
    }
  }
}

shot('$ROLES���������ޤ���');
$ROLES = new Roles();
?>