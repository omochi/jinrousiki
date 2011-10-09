<?php
/*
  ◆人馬 (centaurus_pharmacist)
  ○仕様
  ・処刑投票：投票先が毒を持っていたら死亡する
*/
RoleManager::LoadFile('pharmacist');
class Role_centaurus_pharmacist extends Role_pharmacist{
  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $USERS;

    if(! is_array($stack = $this->GetStack())) return;
    foreach($stack as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      if($USERS->ByRealUname($target_uname)->DistinguishPoison() != 'nothing'){
	$USERS->Kill($USERS->UnameToNumber($uname), 'POISON_DEAD_day');
      }
    }
  }
}
