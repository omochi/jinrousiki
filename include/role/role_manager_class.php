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
      shot(sprintf("ファイルの一覧:{$file}(拡張子 %s)", substr($file, -4, 4)));
      if(substr($file, -4, 4) == '.php'){
        $this->AddRoleFrom($file);
      }
    }
    closedir($dir);
  }

  function AddRoleFrom($file_name){
    shot("{$file_name}からクラスを抽出します。");
    if(preg_match('/.*_class.php$/i', $file_name)){
      shot(">>>失敗:既知のファイルです。");
      return false;
    }
    include_once($file_name);
    $role = substr($file_name, 0, -4);
    $class_name = "Role_{$role}";
    $this->class[$role] = $class_name;
    shot(">>>成功({$class_name})");
  }

  function Instantiate($role_name, $user = null){
    global $USERS, $uname;
    shot("{$role_name}の新しいインスタンスを初期化します。");
    #$USERS->Load();
    return new $this->class[$role_name](isset($user) ? $user : $USERS->ByUname($uname));
  }
}

shot('$ROLESを初期化します。');
$ROLES = new Roles();
?>