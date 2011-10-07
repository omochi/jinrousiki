<?php
/*
  ◆がしゃどくろ (cursed_avenger)
  ○仕様
  ・処刑投票：投票先が生存していたら死の宣告を付加する (人外限定)
*/
RoleManager::LoadFile('avenger');
class Role_cursed_avenger extends Role_avenger{
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
      if($target->IsLive(true) && $target->IsRoleGroup('wolf', 'fox') && ! $target->IsAvoid()){
	$target->AddDoom(4);
      }
    }
  }
}
