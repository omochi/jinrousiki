<?php
/*
  ◆葛の葉 (spell_common)
  ○仕様
 ・処刑投票：魔が言付加 (人外 + 恋人限定)
*/
RoleManager::LoadFile('common');
class Role_spell_common extends Role_common{
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
      if($user->IsLive(true) && ($user->IsRoleGroup('wolf', 'fox') || $user->IsLovers())){
	$user->AddRole('cute_camouflage');
      }
    }
  }
}
