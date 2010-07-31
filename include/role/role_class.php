<?php
//-- 役職コントローラークラス --//
class RoleManager{
  var $path;
  var $loaded;
  var $actor;

  //発言表示
  var $talk_list = array('blinder', 'earplug', 'speaker');

  //発言変換
  var $say_list = array('rainbow', 'weekly', 'grassy', 'invisible', 'mower', 'silent',
			'side_reverse', 'line_reverse', 'actor');

  //声量
  var $voice_list = array('strong_voice', 'normal_voice', 'weak_voice', 'inside_voice',
			  'outside_voice', 'upper_voice', 'downer_voice', 'random_voice');

  //処刑投票
  var $vote_do_list = array('authority', 'critical_voter', 'random_voter', 'watcher', 'panelist');

  //処刑得票
  var $voted_list = array('upper_luck', 'downer_luck', 'star', 'disfavor', 'critical_luck',
			  'random_luck');

  //処刑投票系能力者
  var $vote_ability_list = array('saint', 'executor', 'agitate_mad', 'impatience', 'authority',
				 'rebel', 'decide', 'plague', 'good_luck', 'bad_luck');

  //反逆者判定
  var $rebel_list = array('rebel');

  //処刑者決定 (順番依存あり)
  var $vote_kill_list = array('decide', 'bad_luck', 'impatience', 'good_luck', 'plague',
			      'executor', 'saint', 'agitate_mad');

  //ショック死
  var $sudden_death_list = array('febris', 'death_warrant', 'chicken', 'rabbit', 'perverseness',
				 'flattery', 'impatience', 'celibacy', 'nervy', 'androphobia',
				 'gynophobia', 'panelist');

  function RoleManager(){ $this->__construct(); }
  function __construct(){
    $this->path = JINRO_INC . '/role';
    $this->loaded->file = array();
    $this->loaded->class = array();
  }

  function Load($type){
    $stack = array();
    foreach($this->GetList($type) as $role){
      if(! $this->actor->IsRole($role)) continue;
      $stack[] = $role;
      $this->LoadFile($role);
      $this->LoadClass($role, 'Role_' . $role);
    }
    return $this->GetFilter($stack);
  }

  function LoadFile($name){
    if(is_null($name) || in_array($name, $this->loaded->file)) return false;
    require_once($this->path . '/' . $name . '.php');
    $this->loaded->file[] = $name;
    return true;
  }

  function LoadClass($name, $class){
    if(is_null($name) || in_array($name, $this->loaded->class)) return false;
    $this->loaded->class[$name] =& new $class();
    return true;
  }

  function LoadFilter($type){
    return $this->GetFilter($this->GetList($type));
  }

  function GetList($type){
    $name = $type . '_list';
    $stack = $this->$name;
    return is_array($stack) ? $stack : array();
  }


  function GetFilter($list){
    $stack = array();
    foreach($list as $key){ //順番依存があるので配列関数を使わないで処理する
      if(is_object(($class = $this->loaded->class[$key]))) $stack[] = $class;
    }
    return $stack;
  }

  function GetWhisperingUserInfo($role, &$class){
    global $SELF;

    switch($role){
    case 'common': //共有者のささやき
      if($SELF->IsRole('dummy_common', 'deep_sleep')) return false; //夢共有者・爆睡者には見えない
      $class = 'talk-common';
      return '共有者の小声';

    case 'wolf': //人狼の遠吠え
      if($SELF->IsRole('mind_scanner', 'deep_sleep')) return false; //さとり・爆睡者には見えない
      return '狼の遠吠え';
    }
    return false;
  }

  function GetWhisperingSound($role, $talk, &$class){
    global $MESSAGE;

    switch($role){
    case 'common':
      $class = 'say-common';
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
