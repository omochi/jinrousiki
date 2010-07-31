<?php
/*
  ����ư�� (agitate_mad)
  ������
  ���跺��ɼ���ɹ������鼫ʬ����ɼ���跺�����Ĥ��ޤȤ�ƥ���å��व����
*/
class Role_agitate_mad extends Role{
  function Role_agitate_mad(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function SetVoteAbility($uname){
    global $ROLES, $USERS;

    $user = $USERS->ByRealUname($ROLES->actor->uname);
    if($user->IsRole('agitate_mad')) $ROLES->stack->agitate_mad[] = $user->uname;
  }

  function DecideVoteKill(&$uname){
    global $ROOM, $ROLES, $USERS;

    if($uname != '' || ! is_array($ROLES->stack->agitate_mad)) return;
    $stack = array();
    foreach($ROLES->stack->agitate_mad as $actor_uname){ //��¿��ɼ�Ԥ���ɼ������ư�Ԥ���ɼ������
      $target = $USERS->ByVirtualUname($ROOM->vote[$actor_uname]['target_uname']);
      if(in_array($target->uname, $ROLES->stack->max_voted)){ //��¿��ɼ�ԥꥹ�Ȥϲ��ۥ桼��
	$stack[$target->uname] = true;
      }
    }
    if(count($stack) != 1) return; //�оݤ��ͤ˸���Ǥ�����Τ�ͭ��
    $uname = array_shift(array_keys($stack));
    foreach($ROLES->stack->max_voted as $target_uname){
      if($target_uname != $uname){ //$target_uname �ϲ��ۥ桼��
	$USERS->SuddenDeath($USERS->ByRealUname($target_uname)->user_no, 'SUDDEN_DEATH_AGITATED');
      }
    }
  }
}
