<?php
/*
  ◆霊能者 (necromancer)
  ○仕様
  ・霊能：通常
*/
class Role_necromancer extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2) OutputSelfAbilityResult(strtoupper($this->role) . '_RESULT');
  }

  function Necromancer($user, $flag){ return $flag ? 'stolen' : $user->DistinguishNecromancer(); }
}
