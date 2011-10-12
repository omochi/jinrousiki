<?php
/*
  ◆口寄せ (mind_evoke)
  ○仕様
*/
class Role_mind_evoke extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;
    if($ROOM->date > 1) parent::OutputAbility();
  }
}
