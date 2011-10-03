<?php
/*
  ◆子狐 (child_fox)
  ○仕様
  ・占い：通常
*/
RoleManager::LoadFile('fox');
class Role_child_fox extends Role_fox{
  public $mix_in = 'mage';
  public $result = 'CHILD_FOX_RESULT';
  public $mage_failed = 'failed';
  function __construct(){ parent::__construct(); }

  function OutputFoxAbility(){
    global $ROOM;

    if(is_null($this->result)) return;
    if($ROOM->date > 1) OutputSelfAbilityResult($this->result); //占い結果
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'mage_do', 'CHILD_FOX_DO'); //投票
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
