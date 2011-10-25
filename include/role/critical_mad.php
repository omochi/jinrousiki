<?php
/*
  ◆釣瓶落とし (critical_mad)
  ○仕様
  ・処刑投票：投票先が生存していたら痛恨を付加する
*/
RoleManager::LoadFile('corpse_courier_mad');
class Role_critical_mad extends Role_corpse_courier_mad{
  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $USERS;
    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && ! $target->IsAvoid()) $target->AddRole('critical_luck');
    }
  }
}
