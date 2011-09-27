<?php
/*
  ◆狡狐 (trap_fox)
  ○仕様
*/
RoleManager::LoadFile('fox');
RoleManager::LoadFile('trap_mad');
class Role_trap_fox extends Role_fox{
  public $mix_in = 'trap_mad';
  function __construct(){ parent::__construct(); }

  function OutputFoxAbility(){ $this->OutputTrapAbility(); }
}
