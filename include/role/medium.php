<?php
/*
  ◆巫女 (medium)
  ○仕様
*/
class Role_medium extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 1) OutputSelfAbilityResult('MEDIUM_RESULT');
  }
}
