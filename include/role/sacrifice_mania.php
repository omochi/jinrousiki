<?php
/*
  ◆影武者 (sacrifice_mania)
  ○仕様
  ・追加役職：庇護者
  ・人狼襲撃耐性：常時無効
*/
class Role_sacrifice_mania extends Role{
  function __construct(){ parent::__construct(); }

  function AddRole($role){ return $role . ' ' . $this->GetActor()->GetID('protected'); }

  function WolfEatResist(){ return true; }
}
