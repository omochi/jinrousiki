<?php
/*
  ◆死の宣告 (death_warrant)
  ○仕様
  ・ショック死：発動当日
*/
class Role_death_warrant extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->IsDoom()) $reason = 'WARRANT';
  }
}
