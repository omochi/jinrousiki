<?php
/*
  ◆黒衣 (wirepuller_mania)
  ○仕様
  ・追加役職：入道
*/
class Role_wirepuller_mania extends Role{
  function __construct(){ parent::__construct(); }

  function AddRole($role){ return $role . ' ' . $this->GetActor()->GetID('wirepuller_luck'); }
}
