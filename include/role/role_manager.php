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
      shot(sprintf("ファイルの一覧:{$file}(拡張子 %s)", substr($file, -4, 4)));
      if (substr($file, -4, 4) == '.php'){
        include_once($file);
        $this->AddRoleFrom($file);
      }
    }
    closedir($dir);
  }

  function AddRoleFrom($filename){
    shot("{$filename}からクラスを抽出します。");
    if (preg_match('/.*_class.php$/i', $filename)){
      shot(">>>失敗:既知のファイルです。");
      return false;
    }
    $role = substr($filename, 0, -4);
    $classname = "Role_{$role}";
    $this->class[$role] = $classname;
    shot(">>>成功({$classname})");
  }

  function Instantiate($rolename, $user = null){
    global $USERS, $uname;
    shot("{$rolename}の新しいインスタンスを初期化します。");
    LoadUsers();
    return new $this->class[$rolename](isset($user) ? $user : $USERS->ByUname($uname));
  }
}

shot('$ROLESを初期化します。');
$ROLES = new Roles();
?>