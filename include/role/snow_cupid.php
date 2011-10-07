<?php
/*
  ◆寒戸婆 (snow_cupid)
  ○仕様
  ・処刑投票：投票先が恋人で生存していたら凍傷を付加する
*/
RoleManager::LoadFile('cupid');
class Role_snow_cupid extends Role_cupid{
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
      if($target->IsLive(true) && $target->IsLovers()) $target->AddDoom(1, 'frostbite');
    }
  }
}
