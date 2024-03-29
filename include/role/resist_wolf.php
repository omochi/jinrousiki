<?php
/*
  ◆抗毒狼 (resist_wolf)
  ○仕様
  ・毒対象選出 (襲撃)：本人固定
  ・毒死：回避 (一回限定)
*/
RoleManager::LoadFile('wolf');
class Role_resist_wolf extends Role_wolf {
  function GetPoisonEatTarget() { return $this->GetWolfVoter(); }

  function PoisonDead() {
    $actor = $this->GetActor();
    $actor->IsActive() ? $actor->LostAbility() : parent::PoisonDead();
  }
}
