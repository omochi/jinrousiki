<?php
/*
  ◆門番 (gatekeeper_guard)
  ○仕様
  ・狩り：なし
*/
RoleManager::LoadFile('guard');
class Role_gatekeeper_guard extends Role_guard{
  function __construct(){ parent::__construct(); }

  function SetGuardTarget($uname){
    global $ROLES;

    if(! parent::SetGuardTarget($uname)) return false;
    $ROLES->stack->gatekeeper_guard[$this->GetActor()->uname] = $uname;
    return true;
  }

  function IsHuntTarget($user){ return false; }
}
