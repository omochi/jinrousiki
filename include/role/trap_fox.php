<?php
/*
  ◆狡狐 (trap_fox)
  ○仕様
*/
RoleManager::LoadFile('fox');
class Role_trap_fox extends Role_fox{
  public $mix_in = 'trap_mad';
  function __construct(){ parent::__construct(); }

  function OutputFoxAbility(){ $this->filter->OutputAction(); }

  function SetVoteNight(){ $this->filter->SetVoteNight(); }

  function IsVoteCheckbox($user, $live){ return $this->filter->IsVoteCheckbox($user, $live); }

  function IgnoreVoteNight($user, $live){ return $this->filter->IgnoreVoteNight($user, $live); }
}
