<?php
/*
  ��ŷ�ٵ� (perverseness)
  ������
  ����ʬ����ɼ���ʣ���οͤ���ɼ���Ƥ����饷��å��ह��
*/
class Role_perverseness extends Role{
  function Role_perverseness(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROLES;
    if($reason == '' && $ROLES->stack->count[$ROLES->stack->target[$ROLES->actor->uname]] > 1){
      $reason = 'PERVERSENESS';
    }
  }
}
