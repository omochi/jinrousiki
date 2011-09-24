<?php
/*
  ◆門番 (gatekeeper_guard)
  ○仕様
  ・狩り：なし
*/
RoleManager::LoadFile('guard');
class Role_gatekeeper_guard extends Role_guard{
  function __construct(){ parent::__construct(); }

  function IsHuntTarget($user){ return false; }
}
