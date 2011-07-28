<?php
/*
  ◆忍者 (fend_guard)
  ○仕様
  ・人狼襲撃耐性：無効 (一回限定)
  ・護衛失敗：通常
  ・護衛処理：なし
  ・狩り：通常
*/
class Role_fend_guard extends Role{
  function __construct(){ parent::__construct(); }

  function WolfEatResist(){
    if(! $this->GetActor()->IsActive()) return false;
    $this->GetActor()->LostAbility();
    return true;
  }

  function GuardFailed(){ return false; }

  function GuardAction($user, $flag = false){}

  function IsHuntTarget($user){ return $user->IsHuntTarget(); }
}
