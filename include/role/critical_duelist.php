<?php
/*
  ◆剣闘士 (critical_duelist)
  ○仕様
  ・追加役職：なし
  ・処刑投票：5% の確率で投票数が +100 される
*/
class Role_critical_duelist extends Role{
  function __construct(){ parent::__construct(); }

  function AddRivalRole(&$role, $user, $flag){}

  function FilterVoteDo(&$vote_number){
    if(mt_rand(1, 100) <= 5) $vote_number += 100;
  }
}
