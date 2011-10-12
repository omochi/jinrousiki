<?php
/*
  ◆子狐 (child_fox)
  ○仕様
  ・人狼襲撃耐性：無し
  ・占い：通常
*/
RoleManager::LoadFile('fox');
class Role_child_fox extends Role_fox{
  public $mix_in = 'mage';
  public $resist_wolf = false;
  public $action = 'CHILD_FOX_DO';
  public $result = 'CHILD_FOX_RESULT';
  public $submit = 'mage_do';
  public $mage_failed = 'failed';
  function __construct(){ parent::__construct(); }

  function OutputFoxAbility(){
    global $ROOM;

    if(is_null($this->result)) return;
    if($ROOM->date > 1) OutputSelfAbilityResult($this->result); //占い結果
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', $this->submit, $this->action); //投票
  }

  function Mage($user){
    if($this->IsJammer($user)){
      return $this->SaveMageResult($user, $this->mage_failed, $this->result);
    }
    if($this->IsCursed($user)) return false;
    $result = mt_rand(1, 10) > 7 ? $this->mage_failed : $this->GetMageResult($user);
    $this->SaveMageResult($user, $result, $this->result);
  }

  function GetMageResult($user){ return $user->DistinguishMage(); }
}
