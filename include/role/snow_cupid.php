<?php
/*
  ◆寒戸婆 (snow_cupid)
  ○仕様
  ・処刑投票：凍傷付加 (恋人限定)
*/
RoleManager::LoadFile('cupid');
class Role_snow_cupid extends Role_cupid{
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
      if($user->IsLive(true) && $user->IsLovers()) $user->AddDoom(1, 'frostbite');
    }
  }
}
