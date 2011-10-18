<?php
/*
  ◆神主 (bacchus_medium)
  ○仕様
  ・処刑投票：投票先が鬼陣営ならショック死させる
*/
RoleManager::LoadFile('medium');
class Role_bacchus_medium extends Role_medium{
  public $sudden_death = 'DRUNK';
  function __construct(){ parent::__construct(); }

  function SetVoteDay($uname){
    $this->InitStack();
    if($this->IsRealActor()) $this->AddStack($uname);
  }

  function VoteAction(){
    global $USERS;
    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $this->ActiveSuddenDeath($USERS->ByRealUname($target_uname));
    }
  }

  protected function ActiveSuddenDeath($user){
    if($user->IsLive(true) && $user->IsOgre()) $this->SuddenDeathKill($user->user_no);
  }
}
