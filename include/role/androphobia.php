<?php
/*
  ���������ݾ� (androphobia)
  ������
  ����������ɼ�����饷��å��ह��
*/
class Role_androphobia extends Role{
  function Role_androphobia(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROLES, $USERS;
    if($reason == '' &&
       $USERS->ByRealUname($ROLES->stack->target[$ROLES->actor->uname])->sex == 'male'){
      $reason = 'ANDROPHOBIA';
    }
  }
}
