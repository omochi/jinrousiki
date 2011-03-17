<?php
/*
  ◆ウサギ (rabbit)
  ○仕様
  ・投票されていなかったらショック死する
*/
class Role_rabbit extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetVotedCount() == 0) $reason = 'RABBIT';
  }
}
