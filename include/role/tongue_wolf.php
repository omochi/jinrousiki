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

  protected function OutputResult(){
    global $ROOM;
    if($ROOM->date > 1) OutputSelfAbilityResult($this->result);
  }

  function WolfKill($user){
    global $ROOM;

    parent::WolfKill($user);
    $actor = $this->GetWolfVoter();
    if($user->IsRole('human')) $actor->LostAbility(); //村人なら能力失効
    $ROOM->SystemMessage($actor->GetHandleName($user->uname, $user->main_role), $this->result);
  }
}
