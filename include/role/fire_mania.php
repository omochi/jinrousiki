<?php
/*
  ◆青行灯 (fire_mania)
  ○仕様
  ・追加役職：鬼火
*/
class Role_fire_mania extends Role{
  function __construct(){ parent::__construct(); }

  function AddRole($role){ return $role . ' wisp'; }
}
