<?php
/*
  ◆縁切地蔵 (divorce_jealousy)
  ○仕様
  ・投票者の恋人に一定確率で恋色迷彩を付加
*/
class Role_divorce_jealousy extends RoleVoteAbility{
  var $data_type = 'array';
  var $init_stack = true;

  function __construct(){ parent::__construct(); }

  function VoteKillReaction(){
    global $ROLES, $USERS;

    foreach($ROLES->stack->{$this->role} as $uname){
      if($uname == $ROLES->stack->vote_kill_uname) continue;

      foreach($this->GetVotedUname($uname) as $voted_uname){
	$user = $USERS->ByRealUname($voted_uname);
	if($user->IsLive(true) && $user->IsLovers() && mt_rand(1, 10) > 7){
	  $user->AddRole('passion');
	}
      }
    }
  }
}
