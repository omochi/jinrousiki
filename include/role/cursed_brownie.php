<?php
/*
  ◆祟神 (cursed_brownie)
  ○仕様
  ・処刑得票：死の宣告 (一定確率)
  ・人狼襲撃：死の宣告
*/
class Role_cursed_brownie extends Role{
  function __construct(){ parent::__construct(); }

  function SetVoteDay($uname){
    global $USERS;
    if($USERS->ByRealUname($this->GetUname())->IsRole(true, $this->role)) $this->AddStack($uname);
  }

  function VoteKillReaction(){
    global $USERS;

    if(! is_array($stack = $this->GetStack())) return;
    foreach(array_keys($stack) as $uname){
      foreach($this->GetVotedUname($uname) as $voted_uname){
	$user = $USERS->ByRealUname($voted_uname);
	if($user->IsLive(true) && ! $user->IsAvoid() && mt_rand(1, 10) > 7) $user->AddDoom(2);
      }
    }
  }

  function WolfEatCounter($user){ $user->AddDoom(2); }
}
