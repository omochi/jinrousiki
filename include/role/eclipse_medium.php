<?php
/*
  ◆蝕巫女 (eclipse_medium)
  ○仕様
  ・ショック死：再投票
*/
class Role_eclipse_medium extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetVoteKillUname() == '') $reason = 'SEALED';
  }
}
