<?php
/*
  ◆幸運 (good_luck)
  ○仕様
  ・処刑投票が拮抗したら自分が候補から除外される
*/
class Role_good_luck extends Role{
  function Role_good_luck(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function SetVoteAbility($uname){
    global $ROLES;
    $ROLES->stack->good_luck = $ROLES->actor->uname;
  }

  function DecideVoteKill(&$uname){
    global $ROLES;

    if($uname != '' ||
       ($key = array_search($ROLES->stack->good_luck, $ROLES->stack->vote_possible)) === false){
      return;
    }
    unset($ROLES->stack->vote_possible[$key]);
    if(count($ROLES->stack->vote_possible) == 1){ //この時点で候補が一人なら処刑者決定
      $uname = array_shift($ROLES->stack->vote_possible);
    }
  }
}
