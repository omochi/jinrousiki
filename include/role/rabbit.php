<?php
/*
  �������� (rabbit)
  ������
  ����ɼ����Ƥ��ʤ��ä��饷��å��ह��
*/
class Role_rabbit extends Role{
  function Role_rabbit(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROLES;
    if($reason == '' && $ROLES->stack->count[$ROLES->actor->uname] == 0) $reason = 'RABBIT';
  }
}
