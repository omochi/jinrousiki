<?php
/*
  ◆縁切地蔵 (divorce_jealousy)
  ○仕様
  ・処刑得票：恋色迷彩付加 (恋人・一定確率)
*/
RoleManager::LoadFile('jealousy');
class Role_divorce_jealousy extends Role_jealousy{
  function __construct(){ parent::__construct(); }

  function VoteKillReaction(){
    global $USERS;

    if(! is_array($stack = $this->GetStack())) return;
    foreach($stack as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;

      foreach($this->GetVotedUname($uname) as $voted_uname){
	$user = $USERS->ByRealUname($voted_uname);
	if($user->IsLive(true) && $user->IsLovers() && mt_rand(1, 10) > 7){
	  $user->AddRole('passion');
	}
      }
    }
  }
}
