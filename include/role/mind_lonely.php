<?php
/*
  ◆はぐれ者 (mind_lonely)
  ○仕様
*/
class Role_mind_lonely extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;
    if($ROOM->date > 1) parent::OutputAbility();
  }
}
