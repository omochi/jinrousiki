<?php
/*
  ◆青行灯 (fire_mania)
  ○仕様
  ・追加役職：鬼火
*/
RoleManager::LoadFile('unknown_mania');
class Role_fire_mania extends Role_unknown_mania{
  function __construct(){ parent::__construct(); }

  function GetCopyRole($user){ return 'wisp'; }
}
