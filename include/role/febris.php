<?php
/*
  ◆熱病 (febris)
  ○仕様
  ・ショック死：発動当日
*/
class Role_febris extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->IsDoom()) $reason = 'FEBRIS';
  }
}
