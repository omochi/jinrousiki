<?php
/*
  ◆舌禍狼 (tongue_wolf)
  ○仕様
*/
RoleManager::LoadFile('wolf');
class Role_tongue_wolf extends Role_wolf{
  public $result = 'TONGUE_WOLF_RESULT';
  function __construct(){ parent::__construct(); }

  function OutputWolfAbility(){
    global $ROOM;
    if($ROOM->date > 1) OutputSelfAbilityResult($this->result);
  }
}
