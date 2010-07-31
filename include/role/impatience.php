<?php
/*
  ◆短気 (impatience)
  ○仕様
  ・優先順位が低めの決定者相当
  ・再投票になったらショック死する
*/
class Role_impatience extends Role{
  function Role_impatience(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function SetVoteAbility($uname){
    global $ROLES;
    $ROLES->stack->impatience = $uname;
  }

  function DecideVoteKill(&$uname){
    global $ROLES;
    if($uname == '' && in_array($ROLES->stack->impatience, $ROLES->stack->vote_possible)){
      $uname = $ROLES->stack->impatience;
    }
  }

  function FilterSuddenDeath(&$reason){
    global $ROLES;
    if($reason == '' && $ROLES->stack->revote) $reason = 'IMPATIENCE';
  }
}
