<?php
/*
  ◆ウサギ (rabbit)
  ○仕様
  ・ショック死：無得票
*/
class Role_rabbit extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetVotedCount() == 0) $reason = 'RABBIT';
  }
}
