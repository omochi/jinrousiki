<?php
/*
  ◆神主 (bacchus_medium)
  ○仕様
  ・処刑投票先が鬼陣営ならショック死させる
*/
class Role_bacchus_medium extends RoleVoteAbility{
  var $data_type = 'action';

  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $ROLES, $USERS;

    foreach($ROLES->stack->bacchus_medium as $uname => $target_uname){
      if($uname == $ROLES->stack->vote_kill_uname) continue;

      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && $target->IsOgre()){
	$USERS->SuddenDeath($target->user_no, 'SUDDEN_DEATH_DRUNK');
      }
    }
  }
}
