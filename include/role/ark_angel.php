<?php
/*
  ◆大天使 (ark_angel)
  ○仕様
  ・追加役職：なし
  ・共感者判定：無効
*/
class Role_ark_angel extends Role{
  function __construct(){ parent::__construct(); }

  function AddLoversRole(&$role, $user, $flag){ return true; }

  function IsSympathy($lovers_a, $lovers_b){ return false; }
}
