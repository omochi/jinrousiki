<?php
/*
  ◆祟神 (divorce_jealousy)
  ○仕様
  ・投票者の恋人に一定確率で恋色迷彩を付加
*/
class Role_divorce_jealousy extends RoleVoteAbility{
  var $data_type = 'array';

  function __construct(){ parent::__construct(); }

  function VotedReaction($vote_target_list){
    global $ROLES, $USERS;

    foreach($ROLES->stack->divorce_jealousy as $uname){
      if($uname == $ROLES->stack->vote_kill_uname) continue;

      foreach(array_keys($vote_target_list, $uname) as $voted_uname){
	$voted_user = $USERS->ByRealUname($voted_uname);
	if($voted_user->IsLive(true) && $voted_user->IsLovers() && mt_rand(1, 10) > 7){
	  $voted_user->AddRole('passion');
	}
      }
    }
  }
}
