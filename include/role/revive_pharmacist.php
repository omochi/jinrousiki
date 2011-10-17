<?php
/*
  ◆仙人 (revive_pharmacist)
  ○仕様
  ・ショック死抑制
  ・人狼襲撃：蘇生
*/
RoleManager::LoadFile('pharmacist');
class Role_revive_pharmacist extends Role_pharmacist{
  function __construct(){ parent::__construct(); }

  //復活判定
  function IsResurrect(){
    $actor = $this->GetActor();
    return $actor->IsDead(true) && ! $actor->IsDummyBoy() && ! $actor->IsLovers() &&
      $actor->wolf_killed  && ! $this->GetWolfVoter()->IsSiriusWolf();
  }

  //復活処理
  function Resurrect(){
    $user = $this->GetActor();
    if($this->IsResurrect() && $user->IsActive()){
      $user->Revive();
      $user->LostAbility();
    }
  }
}
