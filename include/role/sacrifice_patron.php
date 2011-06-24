<?php
/*
  ◆身代わり地蔵 (sacrifice_patron)
  ○仕様
  ・追加役職：庇護者
  ・人狼襲撃耐性：常時無効
*/
class Role_sacrifice_patron extends Role{
  function __construct(){ parent::__construct(); }

  function AddSupportedRole(&$role, $user){ $role .= ' ' . $this->GetActor()->GetID('protected'); }

  function WolfEatResist(){ return true; }
}
