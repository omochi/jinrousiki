<?php
/*
  ◆後援者 (patron)
  ○仕様
  ・追加役職：なし
*/
class Role_patron extends Role{
  function __construct(){ parent::__construct(); }

  function AddSupportedRole(&$role, $user){ return true; }
}
