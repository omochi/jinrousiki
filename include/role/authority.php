<?php
/*
  �����ϼ� (authority)
  ������
  ����ɼ���� +1 �����
  ��ȿ�ռԤ�Ʊ���ͤ���ɼ����ȣ�ɼ�ˤʤ�
*/
class Role_authority extends Role{
  function Role_authority(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    $vote_number++;
  }

  function SetVoteAbility($uname){
    global $ROLES;
    $ROLES->stack->authority = $ROLES->actor->uname;
    $ROLES->stack->authority_uname = $uname;
  }
}
