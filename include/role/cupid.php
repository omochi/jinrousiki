<?php
/*
  ◆キューピッド (cupid)
  ○仕様
  ・追加役職：なし
*/
class Role_cupid extends Role{
  function __construct(){ parent::__construct(); }

  function AddLoversRole(&$role, $user, $flag){ return true; }
}
