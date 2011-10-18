<?php
/*
  ◆がしゃどくろ (cursed_avenger)
  ○仕様
  ・処刑投票：死の宣告付加 (人外限定)
*/
RoleManager::LoadFile('avenger');
class Role_cursed_avenger extends Role_avenger{
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
      if($user->IsLive(true) && $user->IsRoleGroup('wolf', 'fox') && ! $user->IsAvoid()){
	$user->AddDoom(4);
      }
    }
  }
}
