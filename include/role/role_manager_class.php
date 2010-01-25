<?php
class Roles{
  var $class = array();

  function Roles(){
    shot('$ROLESを初期化します。');
    $this->LoadRoles();
  }

  function __construct(){
    self::Roles();
  }

  function LoadRoles(){
    $dir = opendir(dirname(__FILE__));
    while(($file = readdir($dir)) !== false){
      $str = substr($file, -4, 4);
      shot(sprintf("ファイルの一覧:{$file}(拡張子 %s)", $str));
      if($str == '.php') $this->AddRoleFrom($file);
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

  function Instantiate($role_name, $user = NULL){
    global $USERS, $SELF;
    shot("{$role_name}の新しいインスタンスを初期化します。");
    #$USERS->Load();
    return new $this->class[$role_name](isset($user) ? $user : $SELF);
  }

  function GetWhisperingUserInfo($role_name, &$class_attr){
    if(strpos($role_name, 'common') !== false){
      $class_attr = 'talk-common';
      return '共有者の小声';
    }
    elseif(strpos($role_name, 'wolf') !== false){
      return '狼の遠吠え';
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
?>
