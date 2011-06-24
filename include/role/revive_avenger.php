<?php
/*
  ◆夜刀神 (revive_avenger)
  ○仕様
  ・追加役職：なし
*/
class Role_revive_avenger extends Role{
  function __construct(){ parent::__construct(); }

  function AddEnemyRole(&$role, $user){}
}
