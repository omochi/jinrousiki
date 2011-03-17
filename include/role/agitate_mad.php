<?php
/*
  ◆扇動者 (agitate_mad)
  ○仕様
  ・処刑投票が拮抗したら自分の投票先を処刑し、残りをまとめてショック死させる
*/
class Role_agitate_mad extends RoleVoteAbility{
  var $data_type = 'action';
  var $decide_type = 'same';

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
