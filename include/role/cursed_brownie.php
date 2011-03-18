<?php
/*
  ◆祟神 (cursed_brownie)
  ○仕様
  ・投票者に一定確率で死の宣告を付加
*/
class Role_cursed_brownie extends RoleVoteAbility{
  var $data_type = 'array';
  var $init_stack = true;

  function __construct(){ parent::__construct(); }

  function VoteKillReaction(){
    global $USERS;

    foreach($this->GetStack() as $uname){
      foreach($this->GetVotedUname($uname) as $voted_uname){
	$user = $USERS->ByRealUname($voted_uname);
	if($user->IsLive(true) && ! $user->IsAvoid() && mt_rand(1, 10) > 7) $user->AddDoom(2);
      }
    }
  }
}
