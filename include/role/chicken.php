<?php
/*
  ◆小心者 (chicken)
  ○仕様
  ・ショック死：得票
*/
class Role_chicken extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetVotedCount() > 0) $reason = 'CHICKEN';
  }
}
