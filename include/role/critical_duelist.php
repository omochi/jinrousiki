<?php
/*
  ◆剣闘士 (critical_duelist)
  ○仕様
  ・追加役職：なし
  ・投票数：+100 (5%)
*/
class Role_critical_duelist extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole($user){ return $this->GetActor()->GetID('rival'); }

  function FilterVoteDo(&$vote_number){
    if(mt_rand(1, 100) <= 5) $vote_number += 100;
  }
}
