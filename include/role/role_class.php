<?php
//-- 役職コントローラークラス --//
class RoleManager{
  var $path;
  var $loaded;
  var $actor;

  //発言表示フィルタ
  var $talk_list = array('blinder', 'earplug', 'speaker');

  //発言フィルタ (登録時)
  var $say_filter_list = array('rainbow', 'weekly', 'grassy', 'invisible', 'mower',
			       'silent', 'side_reverse', 'line_reverse');

  //声量フィルタ
  var $voice_list = array('strong_voice', 'normal_voice', 'weak_voice', 'inside_voice',
			  'outside_voice', 'upper_voice', 'downer_voice', 'random_voice');

  function RoleManager(){ $this->__construct(); }
  function __construct(){
    $this->path = JINRO_INC . '/role';
    $this->loaded->file = array();
    $this->loaded->class = array();
  }

  function Load($type){
    $name = $type . '_list';
    $role_list = $this->$name;
    if(! (is_array($role_list) && count($role_list) > 0)) return;

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
    $this->loaded->class[$name] = & new $class();
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

//-- 役職の基底クラス --//
class Role{
  function Role(){ $this->__construct(); }
  function __construct(){}

  //-- 判定用関数 --//
  function Ignored(){
    global $ROOM, $ROLES;
    //return false; //テスト用
    return ! ($ROOM->IsPlaying() && $ROLES->actor->IsLive());
  }

  function IsSameUser($uname){
    global $ROLES;
    return $ROLES->actor->IsSame($uname);
  }
}

//-- 発言フィルタリング用拡張クラス --//
class RoleTalkFilter extends Role{
  var $volume_list = array('weak', 'normal', 'strong');

  function RoleTalkFilter(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function AddTalk($user, $talk, &$user_info, &$volume, &$sentence){}
  function AddWhisper($role, $talk, &$user_info, &$volume, &$sentence){}

  function ChangeVolume($type, &$volume, &$sentence){
    global $MESSAGE;

    if($this->Ignored()) return;
    switch($type){
    case 'up':
      if(($key = array_search($volume, $this->volume_list)) === false) return;
      if(++$key >= count($this->volume_list))
	$sentence = $MESSAGE->howling;
      else
	$volume = $this->volume_list[$key];
      break;

    case 'down':
      if(($key = array_search($volume, $this->volume_list)) === false) return;
      if(--$key < 0)
	$sentence = $MESSAGE->common_talk;
      else
	$volume = $this->volume_list[$key];
      break;
    }
  }
}
