<?php
/*
  ���������ݾ� (gynophobia)
  ������
  ����������ɼ�����饷��å��ह��
*/
class Role_gynophobia extends Role{
  function Role_gynophobia(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROLES, $USERS;
    if($reason == '' &&
       $USERS->ByRealUname($ROLES->stack->target[$ROLES->actor->uname])->sex == 'female'){
      $reason = 'GYNOPHOBIA';
    }
  }
}
