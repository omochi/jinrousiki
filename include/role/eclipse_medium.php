<?php
/*
  ◆蝕巫女 (eclipse_medium)
  ○仕様
  ・ショック死：再投票
*/
RoleManager::LoadFile('medium');
class Role_eclipse_medium extends RoleVoteAbility{
  public $display_role = 'medium';
  public $mix_in = 'medium';
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetVoteKillUname() == '') $reason = 'SEALED';
  }
}
