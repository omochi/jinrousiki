<?php
/*
  ◆舌禍狼 (tongue_wolf)
  ○仕様
  ・襲撃：役職が分かる
*/
RoleManager::LoadFile('wolf');
class Role_tongue_wolf extends Role_wolf{
  public $result = 'TONGUE_WOLF_RESULT';
  function __construct(){ parent::__construct(); }

  function OutputWolfAbility(){
    global $ROOM;
    if($ROOM->date > 1) OutputSelfAbilityResult($this->result);
  }

  function WolfKill($user, &$list){
    global $ROOM;

    parent::WolfKill($user, $list);
    if($user->IsRole('human')) $this->actor->LostAbility(); //村人なら能力失効
    $str = $this->actor->GetHandleName($user->uname, $user->main_role);
    $ROOM->SystemMessage($str, $this->result);
  }
}
