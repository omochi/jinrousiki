<?php
/*
  ◆強毒者 (strong_poison)
  ○仕様
  ・毒：人外カウント
*/
RoleManager::LoadFile('poison');
class Role_strong_poison extends Role_poison {
  public $display_role = 'poison';

  function IsPoisonTarget(User $user) { return $user->IsInhuman(); }
}
