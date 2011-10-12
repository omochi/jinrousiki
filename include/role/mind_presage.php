<?php
/*
  ◆受託者 (mind_presage)
  ○仕様
*/
class Role_mind_presage extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;
    if($ROOM->date > 2) OutputSelfAbilityResult('PRESAGE_RESULT');
  }
}
