<?php
/*
  ◆羊飼い (shepherd_patron)
  ○仕様
  ・追加役職：羊
*/
class Role_shepherd_patron extends Role{
  function __construct(){ parent::__construct(); }

  function AddSupportedRole(&$role, $user){ $role .= ' ' . $this->GetActor()->GetID('mind_sheep'); }
}
