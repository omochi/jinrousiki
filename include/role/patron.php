<?php
/*
  ◆後援者 (patron)
  ○仕様
*/
RoleManager::LoadFile('valkyrja_duelist');
class Role_patron extends Role_valkyrja_duelist{
  public $partner_role   = 'supported';
  public $partner_header = 'patron_target';
  function __construct(){ parent::__construct(); }

  function GetRole($user){
    $role = parent::GetRole($user);
    if(isset($this->patron_role)) $role .= ' ' . $this->GetActor()->GetID($this->patron_role);
    return $role;
  }
}
