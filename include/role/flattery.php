<?php
/*
  �����ޤ��� (flattery)
  ������
  ����ʬ����ɼ���¾�οͤ���ɼ���Ƥ��ʤ���Х���å��ह��
*/
class Role_flattery extends Role{
  function Role_flattery(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROLES;
    if($reason == '' && $ROLES->stack->count[$ROLES->stack->target[$ROLES->actor->uname]] < 2){
      $reason = 'FLATTERY';
    }
  }
}
