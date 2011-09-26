<?php
/*
  ◆憑狼 (possessed_wolf)
  ○仕様
  ・襲撃：憑依
*/
RoleManager::LoadFile('wolf');
class Role_possessed_wolf extends Role_wolf{
  function __construct(){ parent::__construct(); }

  function OutputWolfAbility(){
    global $ROOM;
    if($ROOM->date > 1) OutputPossessedTarget(); //現在の憑依先
  }

  function WolfKill($user, &$list){
    if($user->IsDummyBoy() || $user->IsCamp('fox') || $user->IsPossessedLimited()){ //スキップ判定
      parent::WolfKill($user, $list);
      return;
    }
    $list[$this->GetVoter()->uname] = $user->uname;
    $user->dead_flag = true;
    if($user->IsRole('anti_voodoo')) $this->GetVoter()->possessed_reset = true; //憑依リセット判定
  }
}
