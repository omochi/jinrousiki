<?php
/*
  ◆凍傷 (frostbite)
  ○仕様
  ・発動当日に投票されていなかったらショック死する
*/
class Role_frostbite extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->IsDoom() && $this->GetVotedCount() == 0) $reason = 'FROSTBITE';
  }
}
