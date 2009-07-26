<?php
require_once(dirname(__FILE__).'/role_class.php');

class Roles {
  var $class = array();

  function Roles(){
    $this->loadRoles();
  }

  function __construct(){
    $this->loadRoles();
  }

  function loadRoles(){
    $dir = opendir(dirname(__FILE__));
    while (($file = readdir($dir)) !== false){
      shot(sprintf("�ե�����ΰ���:{$file}(��ĥ�� %s)", substr($file, -4, 4)));
      if (substr($file, -4, 4) == '.php'){
        include_once($file);
        $this->AddRoleFrom($file);
      }
    }
    closedir($dir);
  }

  function AddRoleFrom($filename){
    shot("{$filename}���饯�饹����Ф��ޤ���");
    if (preg_match('/.*_class.php$/i', $filename)){
      shot(">>>����:���ΤΥե�����Ǥ���");
      return false;
    }
    $role = substr($filename, 0, -4);
    $classname = "Role_{$role}";
    $this->class[$role] = $classname;
    shot(">>>����({$classname})");
  }

  function Instantiate($rolename, $user = null){
    global $USERS, $uname;
    shot("{$rolename}�ο��������󥹥��󥹤��������ޤ���");
    LoadUsers();
    return new $this->class[$rolename](isset($user) ? $user : $USERS->ByUname($uname));
  }
}

shot('$ROLES���������ޤ���');
$ROLES = new Roles();
?>