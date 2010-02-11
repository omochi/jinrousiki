<?php
class Role extends DocumentBuilderExtension{
  var $actor;

  function Role($user = NULL){ $this->__construct($user); }

  function __construct($user = NULL){
    $this->actor = $user;
  }

  function Ignored(){
    global $ROOM, $SELF;
    return ! ($ROOM->IsPlaying() && $SELF->IsLive());
  }

  function IsSameUser($uname){
    global $ROLES;
    return $ROLES->actor->IsSame($uname);
  }

  function OutputRoleNotification($writer){ return 'not implemented'; }
  function OutputVoteNotification($writer){ return 'not implemented'; }
  function FilterWords($talk, $date, $situation){ return false; }

  function Say($words, $volume){
    //TODO: 以下に発言用のコードを記述してください。
  }

  function WriteLastWords($content){
    //TODO: 以下に発言用のコードを記述してください。 //EntryLastWords() じゃないの？
  }

  function Object(){
    //TODO: 以下に発言用のコードを記述してください。
  }

  function Vote($target){
    //TODO: 以下に投票用のコードを記述してください。
  }

  function Action($target){
    //TODO: 以下に投票用のコードを記述してください。
  }
}

class RoleManager{
  var $path;
  var $loaded;
  var $actor;

  //発言にフィルタがかかるタイプ
  var $filter_list = array('blinder', 'earplug', 'speaker');

  function RoleManager(){ $this->__construct(); }

  function __construct(){
    $this->path = JINRO_INC . '/role';
    $this->loaded->file = array();
    $this->loaded->class = array();
  }

  function Load($type){
    switch($type){
    case 'talk':
      $role_list = $this->filter_list;
      break;

    default:
      return;
    }

    foreach($role_list as $role){
      if(! $this->actor->IsRole($role)) continue;
      $class = 'Role_' . $role;
      $this->LoadFile($role);
      $this->LoadClass($role, $class);
    }

    return array_intersect_key($this->loaded->class, array_flip($role_list));
  }

  function LoadFile($name){
    if(is_null($name) || in_array($name, $this->loaded->file)) return false;
    require_once($this->path . '/' . $name . '.php');
    $this->loaded->file[] = $name;
  }

  function LoadClass($name, $class){
    if(is_null($name) || in_array($name, $this->loaded->class)) return false;
    $this->loaded->class[$name] = & new $class($this->actor);
    return true;
  }

  function GetWhisperingUserInfo($role, &$class){
    global $SELF;

    switch($role){
    case 'common': //共有者のささやき
      if($SELF->IsRole('dummy_common')) return false; //夢共有者には見えない
      $class = 'talk-common';
      return '共有者の小声';

    case 'wolf': //人狼の遠吠え
      if($SELF->IsRole('mind_scanner')) return false; //さとりには見えない
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
