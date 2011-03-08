<?php
/*
  ◆出題者 (quiz)
  ○仕様
  ・処刑投票が拮抗したら自分の投票先を優先的に処刑する
*/
class Role_quiz extends RoleVoteAbility{
  var $data_type = 'action';

  function __construct(){ parent::__construct(); }

  function DecideVoteKill(&$uname){
    global $ROLES;

    if(parent::DecideVoteKill($uname)) return;
    $stack = array_intersect($ROLES->stack->max_voted, $ROLES->stack->{$this->role});
    if(count($stack) == 1) $uname = array_shift($stack); //対象を一人に固定できる時のみ有効
  }
}
