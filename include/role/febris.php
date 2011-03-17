<?php
/*
  ◆熱病 (febris)
  ○仕様
  ・発動当日ならショック死する
*/
class Role_febris extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->IsDoom()) $reason = 'FEBRIS';
  }
}
