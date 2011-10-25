<?php
/*
  ◆土蜘蛛 (miasma_mad)
  ○仕様
  ・処刑投票：熱病付加 (生存限定)
*/
RoleManager::LoadFile('corpse_courier_mad');
class Role_miasma_mad extends Role_corpse_courier_mad{
  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $USERS;
    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && ! $target->IsAvoid()) $target->AddDoom(1, 'febris');
    }
  }
}
