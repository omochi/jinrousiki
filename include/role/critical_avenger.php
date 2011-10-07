<?php
/*
  ◆狂骨 (critical_avenger)
  ○仕様
  ・処刑投票：投票先が生存していたら痛恨を付加する (釣瓶落とし相当)
*/
RoleManager::LoadFile('avenger');
class Role_critical_avenger extends Role_avenger{
  function __construct(){ parent::__construct(); }

  function SetVoteDay($uname){
    global $USERS;
    if($USERS->ByRealUname($this->GetUname())->IsRole($this->role)) $this->AddStack($uname);
  }

  function VoteAction(){
    global $USERS;

    if(! is_array($stack = $this->GetStack())) return;
    foreach($stack as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && ! $target->IsAvoid()) $target->AddRole('critical_luck');
    }
  }
}
