<?php
/*
  ◆草履大将 (cute_avenger)
  ○仕様
  ・追加役職：なし
*/
class Role_cute_avenger extends Role{
  function __construct(){ parent::__construct(); }

  function AddEnemyRole(&$role, $user){}
}
