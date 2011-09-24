<?php
/*
  ◆侍 (reflect_guard)
  ○仕様
  ・狩り：通常 + 鬼陣営
*/
RoleManager::LoadFile('guard');
class Role_reflect_guard extends Role_guard{
  function __construct(){ parent::__construct(); }

  function IsHuntTarget($user){ return $user->IsHuntTarget() || $user->IsOgre(); }
}
