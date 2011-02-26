<?php
/*
  ◆祟神 (cursed_brownie)
  ○仕様
  ・投票者に一定確率で死の宣告を付加
*/
class Role_cursed_brownie extends RoleVoteAbility{
  var $data_type = 'array';

  function __construct(){ parent::__construct(); }

  function VotedReaction($vote_target_list){
    global $ROLES, $USERS;

    foreach($ROLES->stack->cursed_brownie as $uname){
      foreach(array_keys($vote_target_list, $uname) as $voted_uname){
	$voted_user = $USERS->ByRealUname($voted_uname);
	if($voted_user->IsLive(true) && ! $voted_user->IsAvoid() && mt_rand(1, 10) > 7){
	  $voted_user->AddDoom(2);
	}
      }
    }
  }
}
