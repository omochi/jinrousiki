<?php
/*
  ��û�� (impatience)
  ������
  ��ͥ���̤����η��������
  ������ɼ�ˤʤä��饷��å��ह��
*/
class Role_impatience extends Role{
  function Role_impatience(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function SetVoteAbility($uname){
    global $ROLES;
    $ROLES->stack->impatience = $uname;
  }

  function DecideVoteKill(&$uname){
    global $ROLES;
    if($uname == '' && in_array($ROLES->stack->impatience, $ROLES->stack->vote_possible)){
      $uname = $ROLES->stack->impatience;
    }
  }

  function FilterSuddenDeath(&$reason){
    global $ROLES;
    if($reason == '' && $ROLES->stack->revote) $reason = 'IMPATIENCE';
  }
}
