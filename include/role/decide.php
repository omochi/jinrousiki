<?php
/*
  ◆決定者 (decide)
  ○仕様
  ・処刑投票が拮抗したら自分の投票先が処刑される
*/
class Role_decide extends Role{
  function Role_decide(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function SetVoteAbility($uname){
    global $ROLES;
    $ROLES->stack->decide = $uname;
  }

  function DecideVoteKill(&$uname){
    global $ROLES;
    if($uname == '' && in_array($ROLES->stack->decide, $ROLES->stack->vote_possible)){
      $uname = $ROLES->stack->decide;
    }
  }
}
