<?php
/*
  �������� (panelist)
  ������
  ������Ԥ���ɼ����ȥ���å��ह��
  ����ɼ���� 0 �Ǹ��ꤵ���
*/
class Role_panelist extends Role{
  function Role_panelist(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    $vote_number = 0;
  }

  function FilterSuddenDeath(&$reason){
    global $ROLES, $USERS;
    if($reason == '' &&
       $USERS->ByUname($ROLES->stack->target[$ROLES->actor->uname])->IsDummyBoy()){
      $reason = 'PANELIST';
    }
  }
}
