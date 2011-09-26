<?php
/*
  ◆抗毒狼 (resit_wolf)
  ○仕様
  ・毒対象選出(襲撃時)：本人固定
  ・毒死：回避 (一回限定)
*/
RoleManager::LoadFile('wolf');
class Role_resist_wolf extends Role_wolf{
  function __construct(){ parent::__construct(); }

  function GetPoisonTarget(){ return $this->GetVoter(); }

  function PoisonDead(){
    $this->GetActor()->IsActive() ? $this->GetActor()->LostAbility() : parent::PoisonDead();
  }
}
