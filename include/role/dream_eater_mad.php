<?php
/*
  ◆獏 (dream_eater_mad)
  ○仕様
*/
class Role_dream_eater_mad extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 1 && $ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'dream_eat', 'DREAM_EAT');
  }
}
