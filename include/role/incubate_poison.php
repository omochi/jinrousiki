<?php
/*
  ◆潜毒者 (incubate_poison)
  ○仕様
  ・毒：人狼系 + 妖狐陣営 (5日目以降)
*/
RoleManager::LoadFile('poison');
class Role_incubate_poison extends Role_poison{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 4) OutputAbilityResult('ability_poison', NULL); //能力発現
  }

  function IsPoisonTarget($user){ return $user->IsRoleGroup('wolf', 'fox'); }
}
