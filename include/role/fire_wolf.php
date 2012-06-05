<?php
/*
  ◆火狼 (fire_wolf)
  ○仕様
  ・妖狐襲撃：天火 (一回限定)
  ・襲撃：天火 (一回限定)
*/
RoleManager::LoadFile('wolf');
class Role_fire_wolf extends Role_wolf {
  function FoxEatAction(User $user) {
    if (! $this->GetWolfVoter()->IsActive()) return false;
    $user->AddRole('black_wisp');
    $this->GetWolfVoter()->LostAbility();
  }

  function WolfEatAction(User $user) {
    if (! $this->GetWolfVoter()->IsActive()) return false;
    $user->AddRole('black_wisp');
    $user->wolf_eat = true; //襲撃は成功扱い
    $this->GetWolfVoter()->LostAbility();
    return true;
  }
}
