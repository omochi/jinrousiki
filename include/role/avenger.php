<?php
/*
  ◆復讐者 (avenger)
  ○仕様
  ・追加役職：なし
*/
class Role_avenger extends Role{
  function __construct(){ parent::__construct(); }

  function AddEnemyRole(&$role, $user){ return true; }
}
