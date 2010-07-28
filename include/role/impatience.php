<?php
/*
  ◆短気 (impatience)
  ○仕様
  ・再投票になったらショック死する
*/
class Role_impatience extends Role{
  function Role_impatience(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROLES;
    if($reason == '' && $ROLES->stack->revote) $reason = 'IMPATIENCE';
  }
}
