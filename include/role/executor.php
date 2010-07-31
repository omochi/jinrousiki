<?php
/*
  �����Լ� (executor)
  ������
  ���跺��ɼ���ɹ������鼫ʬ����ɼ�褬��¼�ͤξ��Τ߽跺�����
*/
class Role_executor extends Role{
  function Role_executor(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function SetVoteAbility($uname){
    global $ROLES, $USERS;

    $user = $USERS->ByRealUname($ROLES->actor->uname);
    if($user->IsRole('executor')) $ROLES->stack->executor[] = $user->uname;
  }

  function DecideVoteKill(&$uname){
    global $ROOM, $ROLES, $USERS;

    if($uname != '' || ! is_array($ROLES->stack->executor)) return;
    $stack = array();
    foreach($ROLES->stack->executor as $actor_uname){ //��¿��ɼ�Ԥ���ɼ�������ԼԤ���ɼ������
      $target = $USERS->ByVirtualUname($ROOM->vote[$actor_uname]['target_uname']);
      if(in_array($target->uname, $ROLES->stack->max_voted) &&
	 $target->GetCamp(true) != 'human'){ //��¿��ɼ�ԥꥹ�Ȥϲ��ۥ桼��
	$stack[$target->uname] = true;
      }
    }
    //PrintData($stack);
    //�оݤ��ͤ˸���Ǥ�����Τ�ͭ��
    if(count($stack) == 1) $uname = array_shift(array_keys($stack));
  }
}
