<?php
/*
  �����¿� (plague)
  ������
  ���跺��ɼ���ɹ������鼫ʬ����ɼ�褬���䤫����������
*/
class Role_plague extends Role{
  function Role_plague(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function SetVoteAbility($uname){
    global $ROLES;
    $ROLES->stack->plague = $uname;
  }

  function DecideVoteKill(&$uname){
    global $ROLES;

    if($uname != '' ||
       ($key = array_search($ROLES->stack->plague, $ROLES->stack->vote_possible)) === false){
      return;
    }
    unset($ROLES->stack->vote_possible[$key]);
    if(count($ROLES->stack->vote_possible) == 1){ //���λ����Ǹ��䤬��ͤʤ�跺�Է���
      $uname = array_shift($ROLES->stack->vote_possible);
    }
  }
}
