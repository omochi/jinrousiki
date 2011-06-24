<?php
/*
  ◆戦乙女 (valkyrja_duelist)
  ○仕様
  ・追加役職：なし
*/
class Role_valkyrja_duelist extends Role{
  function __construct(){ parent::__construct(); }

  function AddRivalRole(&$role, $user, $flag){}
}
