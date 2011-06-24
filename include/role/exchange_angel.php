<?php
/*
  ◆魂移使 (exchange_angel)
  ○仕様
  ・追加役職：なし
  ・共感者判定：特殊 (集計後)
*/
class Role_exchange_angel extends Role{
  function __construct(){ parent::__construct(); }

  function AddLoversRole(&$role, $user, $flag){}

  function IsSympathy($lovers_a, $lovers_b){ return false; }
}
