<?php
/*
  ◆扇動者 (agitate_mad)
  ○仕様
  ・処刑投票が拮抗したら自分の投票先を処刑し、残りをまとめてショック死させる
*/
class Role_agitate_mad extends RoleVoteAbility{
  var $data_type = 'action';

  function __construct(){ parent::__construct(); }

  function DecideVoteKill(&$uname){
    global $ROLES, $USERS;

    if(parent::DecideVoteKill($uname)) return;
    $stack = array_intersect($ROLES->stack->max_voted, $ROLES->stack->{$this->role});
    if(count($stack) != 1) return; //対象を一人に固定できる時のみ有効
    $uname = array_shift($stack);
    foreach($ROLES->stack->max_voted as $target_uname){
      if($target_uname != $uname){ //$target_uname は仮想ユーザ
	$USERS->SuddenDeath($USERS->ByRealUname($target_uname)->user_no, 'SUDDEN_DEATH_AGITATED');
      }
    }
  }
}
