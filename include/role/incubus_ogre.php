<?php
/*
  ◆般若 (incubus_ogre)
  ○仕様
  ・勝利：生存 + 女性全滅
*/
RoleManager::LoadFile('ogre');
class Role_incubus_ogre extends Role_ogre {
  public $resist_rate  = 40;
  public $reduce_rate  =  2;
  public $reflect_rate = 40;

  function Win($winner) {
    if ($this->IsDead()) return false;
    foreach (DB::$USER->rows as $user) {
      if (! $this->IsActor($user) && $user->IsLive() && $user->IsFemale()) return false;
    }
    return true;
  }
}
