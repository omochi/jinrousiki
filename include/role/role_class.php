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
    //TODO: �ʲ���ȯ���ѤΥ����ɤ򵭽Ҥ��Ƥ���������
  }

  function WriteLastWords($content){
    //TODO: �ʲ���ȯ���ѤΥ����ɤ򵭽Ҥ��Ƥ��������� //EntryLastWords() ����ʤ��Ρ�
  }

  function Object(){
    //TODO: �ʲ���ȯ���ѤΥ����ɤ򵭽Ҥ��Ƥ���������
  }

  function Vote($target){
    //TODO: �ʲ�����ɼ�ѤΥ����ɤ򵭽Ҥ��Ƥ���������
  }

  function Action($target){
    //TODO: �ʲ�����ɼ�ѤΥ����ɤ򵭽Ҥ��Ƥ���������
  }
}

class RoleManager{
  var $path;
  var $loaded;
  var $actor;

  //ȯ���˥ե��륿�������륿����
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
    case 'common': //��ͭ�ԤΤ����䤭
      if($SELF->IsRole('dummy_common')) return false; //̴��ͭ�Ԥˤϸ����ʤ�
      $class = 'talk-common';
      return '��ͭ�Ԥξ���';

    case 'wolf': //��ϵ�α��ʤ�
      if($SELF->IsRole('mind_scanner')) return false; //���Ȥ�ˤϸ����ʤ�
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
