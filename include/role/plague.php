<?php
/*
  ◆疫病神 (plague)
  ○仕様
  ・処刑投票が拮抗したら自分の投票先が候補から除外される
*/
class Role_plague extends Role{
  function Role_plague(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function SetVoteAbility($uname){
    global $ROLES;
    $ROLES->stack->plague = $uname;
  }

  function DecideVoteKill(&$uname){
    global $ROLES;

    if($uname != '' ||
       ($key = array_search($ROLES->stack->plague, $ROLES->stack->vote_possible)) === false){
      return;
    }
    unset($ROLES->stack->vote_possible[$key]);
    if(count($ROLES->stack->vote_possible) == 1){ //この時点で候補が一人なら処刑者決定
      $uname = array_shift($ROLES->stack->vote_possible);
    }
  }
}
