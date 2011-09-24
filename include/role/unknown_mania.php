<?php
/*
  ◆鵺 (unknown_mania)
  ○仕様
  ・追加役職：なし
*/
RoleManager::LoadFile('mania');
class Role_unknown_mania extends Role_mania{
  function __construct(){ parent::__construct(); }

  function AddRole($role){ return $role; }
}
