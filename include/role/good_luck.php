<?php
/*
  ������ (good_luck)
  ������
  ���跺��ɼ���ɹ������鼫ʬ�����䤫����������
*/
class Role_good_luck extends Role{
  function Role_good_luck(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function SetVoteAbility($uname){
    global $ROLES;
    $ROLES->stack->good_luck = $ROLES->actor->uname;
  }

  function DecideVoteKill(&$uname){
    global $ROLES;

    if($uname != '' ||
       ($key = array_search($ROLES->stack->good_luck, $ROLES->stack->vote_possible)) === false){
      return;
    }
    unset($ROLES->stack->vote_possible[$key]);
    if(count($ROLES->stack->vote_possible) == 1){ //���λ����Ǹ��䤬��ͤʤ�跺�Է���
      $uname = array_shift($ROLES->stack->vote_possible);
    }
  }
}
