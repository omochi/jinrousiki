<?php
/*
  ◆雪女 (snow_trap_mad)
  ○仕様
*/
class Role_snow_trap_mad extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 1 && $ROOM->IsNight()){
      OutputVoteMessage('wolf-eat', 'trap_do', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
    }
  }
}
