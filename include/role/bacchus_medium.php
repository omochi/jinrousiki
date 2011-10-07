<?php
/*
  ◆神主 (bacchus_medium)
  ○仕様
  ・処刑投票：投票先が鬼陣営ならショック死させる
*/
RoleManager::LoadFile('medium');
class Role_bacchus_medium extends Role_medium{
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
      if($target->IsLive(true) && $target->IsOgre()){
	$USERS->SuddenDeath($target->user_no, 'SUDDEN_DEATH_DRUNK');
      }
    }
  }
}
