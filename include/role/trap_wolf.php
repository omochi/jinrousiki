<?php
/*
  ◆狡狼 (trap_wolf)
  ○仕様
*/
RoleManager::LoadFile('wolf');
class Role_trap_wolf extends Role_wolf {
  public $ability = 'ability_trap_wolf';
  function __construct(){ parent::__construct(); }

  protected function OutputResult(){
    if (DB::$ROOM->date > 2) OutputAbilityResult($this->ability, null);
  }

  function SetTrap($uname){ $this->AddStack($uname, 'trap'); }
}
