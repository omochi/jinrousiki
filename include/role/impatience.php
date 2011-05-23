<?php
/*
  ◆短気 (impatience)
  ○仕様
  ・優先順位が低めの決定者相当
  ・再投票になったらショック死する
*/
class Role_impatience extends RoleVoteAbility{
  public $data_type = 'target';
  public $decide_type = 'decide';

  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROLES;
    if($reason == '' && $ROLES->stack->vote_kill_uname == '') $reason = 'IMPATIENCE';
  }
}
