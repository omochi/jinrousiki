<?php
/*
  ◆後鬼 (indigo_ogre)
  ○仕様
  ・勝利：生存 + 妖狐陣営全滅
*/
RoleManager::LoadFile('ogre');
class Role_indigo_ogre extends Role_ogre {
  function Win($winner) {
    if ($winner == 'fox' || $this->IsDead()) return false;
    foreach (DB::$USER->rows as $user) {
      if ($user->IsLive() && $user->IsCamp('fox', true)) return false;
    }
    return true;
  }
}
