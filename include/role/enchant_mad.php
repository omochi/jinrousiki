<?php
/*
  ◆狢 (enchant_mad)
  ○仕様
*/
class Role_enchant_mad extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->IsNight()) OutputVoteMessage('fairy-do', 'fairy_do', 'FAIRY_DO');
  }
}
