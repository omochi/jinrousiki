<?php
//-- �򿦥���ȥ��顼���饹 --//
class RoleManager{
  var $path;
  var $loaded;
  var $actor;

  //ȯ��ɽ��
  var $talk_list = array('blinder', 'earplug', 'speaker');

  //ȯ���Ѵ�
  var $say_list = array('rainbow', 'weekly', 'grassy', 'invisible', 'mower', 'silent',
			'side_reverse', 'line_reverse', 'actor');

  //����
  var $voice_list = array('strong_voice', 'normal_voice', 'weak_voice', 'inside_voice',
			  'outside_voice', 'upper_voice', 'downer_voice', 'random_voice');

  //��ɼ
  var $vote_do_list = array('authority', 'critical_voter', 'random_voter', 'watcher', 'panelist');

  //��ɼ
  var $voted_list = array('upper_luck', 'downer_luck', 'star', 'disfavor', 'critical_luck',
			  'random_luck');

  //����å���
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
    $name = $type . '_list';
    $role_list = $this->$name;
    if(! (is_array($role_list) && count($role_list) > 0)) return;

    $target_list = array();
    foreach($role_list as $role){
      if(! $this->actor->IsRole($role)) continue;
      $target_list[] = $role;
      $class = 'Role_' . $role;
      $this->LoadFile($role);
      $this->LoadClass($role, $class);
    }

    return array_intersect_key($this->loaded->class, array_flip($target_list));
  }

  function LoadFile($name){
    if(is_null($name) || in_array($name, $this->loaded->file)) return false;
    require_once($this->path . '/' . $name . '.php');
    $this->loaded->file[] = $name;
  }

  function LoadClass($name, $class){
    if(is_null($name) || in_array($name, $this->loaded->class)) return false;
    $this->loaded->class[$name] =& new $class();
    return true;
  }

  function GetWhisperingUserInfo($role, &$class){
    global $SELF;

    switch($role){
    case 'common': //��ͭ�ԤΤ����䤭
      if($SELF->IsRole('dummy_common', 'deep_sleep')) return false; //̴��ͭ�ԡ�����Ԥˤϸ����ʤ�
      $class = 'talk-common';
      return '��ͭ�Ԥξ���';

    case 'wolf': //��ϵ�α��ʤ�
      if($SELF->IsRole('mind_scanner', 'deep_sleep')) return false; //���Ȥꡦ����Ԥˤϸ����ʤ�
      return 'ϵ�α��ʤ�';
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

//-- �򿦤δ��쥯�饹 --//
class Role{
  function Role(){ $this->__construct(); }
  function __construct(){}

  //-- Ƚ���Ѵؿ� --//
  function Ignored(){
    global $ROOM, $ROLES;
    //return false; //�ƥ�����
    return ! ($ROOM->IsPlaying() && $ROLES->actor->IsLive());
  }

  function IsSameUser($uname){
    global $ROLES;
    return $ROLES->actor->IsSame($uname);
  }
}

//-- ȯ���ե��륿����ѳ�ĥ���饹 --//
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
