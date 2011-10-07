<?php
/*
  ◆弁財天 (sweet_cupid)
  ○仕様
  ・追加役職：両方に共鳴者
  ・処刑投票：投票先が生存していたら恋耳鳴を付加する (魔法あり)
*/
RoleManager::LoadFile('cupid');
class Role_sweet_cupid extends Role_cupid{
  function __construct(){ parent::__construct(); }

  function GetRole($user, $flag){
    return $this->GetActor()->GetID('lovers') . ' ' . $this->GetActor()->GetID('mind_friend');
  }

  function SetVoteDay($uname){
    global $USERS;
    if($USERS->ByRealUname($this->GetUname())->IsRole(true, $this->role)) $this->AddStack($uname);
  }

  function VoteAction(){
    global $USERS;

    if(! is_array($stack = $this->GetStack())) return;
    foreach($stack as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true)) $target->AddRole('sweet_ringing');
    }
  }
}
