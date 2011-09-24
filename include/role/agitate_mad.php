<?php
/*
  ◆扇動者 (agitate_mad)
  ○仕様
  ・決定能力：自分の投票先を処刑し、残りをまとめてショック死させる
*/
class Role_agitate_mad extends RoleVoteAbility{
  public $data_type = 'action';
  public $decide_type = 'same';
  function __construct(){ parent::__construct(); }

  function DecideVoteKill(&$uname){
    global $ROLES, $USERS;

    if(parent::DecideVoteKill($uname)) return;
    foreach($ROLES->stack->max_voted as $target_uname){
      if($target_uname != $uname){ //$target_uname は仮想ユーザ
	$USERS->SuddenDeath($USERS->ByRealUname($target_uname)->user_no, 'SUDDEN_DEATH_AGITATED');
      }
    }
  }
}
