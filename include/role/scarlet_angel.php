<?php
/*
  ◆紅天使 (scarlet_angel)
  ○仕様
  ・追加役職：なし
  ・共感者判定：常時有効
*/
class Role_scarlet_angel extends Role{
  function __construct(){ parent::__construct(); }

  function AddLoversRole(&$role, $user, $flag){}

  function IsSympathy($lovers_a, $lovers_b){ return true; }
}
