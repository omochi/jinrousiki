<?php
/*
  �������� (chicken)
  ������
  ����ɼ����Ƥ����饷��å��ह��
*/
class Role_chicken extends Role{
  function Role_chicken(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROLES;
    if($reason == '' && $ROLES->stack->count[$ROLES->actor->uname] > 0) $reason = 'CHICKEN';
  }
}
