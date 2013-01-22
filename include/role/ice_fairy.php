<?php
/*
  ◆氷妖精 (ice_fairy)
  ○仕様
  ・悪戯：凍傷 (30% で反射)
*/
RoleManager::LoadFile('fairy');
class Role_ice_fairy extends Role_fairy {
  function FairyAction(User $user) {
    $target = Lottery::Percent(30) ? $this->GetActor() : $user;
    $target->AddDoom(1, 'frostbite');
  }
}
