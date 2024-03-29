<?php
/*
  ◆白蔵主 (sacrifice_fox)
  ○仕様
  ・身代わり対象者：子狐系・蝙蝠系
*/
RoleManager::LoadFile('fox');
class Role_sacrifice_fox extends Role_fox {
  public $mix_in = 'protected';
  public $resist_wolf = false;

  function IsSacrifice(User $user) {
    return $user->IsChildFox() || $user->IsMainGroup('chiroptera');
  }
}
