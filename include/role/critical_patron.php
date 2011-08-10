<?php
/*
  ◆ひんな神 (critical_patron)
  ○仕様
  ・追加役職：ひんな持ち
  ・得票数：+100 (5%)
*/
class Role_critical_patron extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole($user){
    return $this->GetActor()->GetID('supported') . ' ' . $this->GetActor()->GetID('occupied_luck');
  }

  function FilterVoted(&$voted_number){
    if(mt_rand(1, 100) <= 5) $voted_number += 100;
  }
}
