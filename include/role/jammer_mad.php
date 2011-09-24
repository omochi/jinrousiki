<?php
/*
  ◆月兎 (jammer_mad)
  ○仕様
*/
class Role_jammer_mad extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'jammer_do', 'JAMMER_MAD_DO');
  }
}
