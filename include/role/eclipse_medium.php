<?php
/*
  ◆蝕巫女 (eclipse_medium)
  ○仕様
  ・ショック死：再投票
*/
RoleManager::LoadFile('medium');
class Role_eclipse_medium extends Role_medium{
  public $display_role = 'medium';
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetStack('vote_kill_uname') == '') $reason = 'SEALED';
  }
}
