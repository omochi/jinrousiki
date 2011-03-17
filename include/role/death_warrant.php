<?php
/*
  ◆死の宣告 (death_warrant)
  ○仕様
  ・発動当日ならショック死する
*/
class Role_death_warrant extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->IsDoom()) $reason = 'WARRANT';
  }
}
