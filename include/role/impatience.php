<?php
/*
  ��û�� (impatience)
  ������
  ������ɼ�ˤʤä��饷��å��ह��
*/
class Role_impatience extends Role{
  function Role_impatience(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROLES;
    if($reason == '' && $ROLES->stack->revote) $reason = 'IMPATIENCE';
  }
}
