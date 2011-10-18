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
    $this->InitStack();
    if($this->IsRealActor()) $this->AddStack($uname);
  }

  function VoteAction(){
    global $USERS;
    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $user = $USERS->ByRealUname($target_uname);
      if($user->IsLive(true) && ! $user->IsAvoid()) $user->AddRole('critical_luck');
    }
  }
}
