<?php
/*
  ◆恋妖精 (sweet_fairy)
  ○仕様
*/
RoleManager::LoadFile('fairy');
class Role_sweet_fairy extends Role_fairy{
  public $action = 'CUPID_DO';
  function __construct(){ parent::__construct(); }

  function IsVote(){
    global $ROOM;
    return $ROOM->date ==1 && $ROOM->IsNight();
  }
}
