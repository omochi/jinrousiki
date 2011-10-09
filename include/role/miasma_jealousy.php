<?php
/*
  ◆蛇姫 (miasma_jealousy)
  ○仕様
  ・処刑投票：熱病付加 (恋人限定・確率)
*/
RoleManager::LoadFile('jealousy');
class Role_miasma_jealousy extends Role_jealousy{
  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $USERS;

    if(! is_array($stack = $this->GetStack())) return;
    foreach($stack as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && $target->IsLovers() && mt_rand(1, 10) > 6){
	$target->AddDoom(1, 'febris');
      }
    }
  }
}
