<?php
/*
  ◆弁財天 (sweet_cupid)
  ○仕様
  ・追加役職：両方に共鳴者
  ・処刑投票：恋耳鳴付加
*/
RoleManager::LoadFile('cupid');
class Role_sweet_cupid extends Role_cupid{
  function __construct(){ parent::__construct(); }

  function AddCupidRole($user, $flag){ $user->AddRole($this->GetActor()->GetID('mind_friend')); }

  function SetVoteDay($uname){
    $this->InitStack();
    if($this->IsRealActor()) $this->AddStack($uname);
  }

  function VoteAction(){
    global $USERS;
    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $user = $USERS->ByRealUname($target_uname);
      if($user->IsLive(true)) $user->AddRole('sweet_ringing');
    }
  }
}
