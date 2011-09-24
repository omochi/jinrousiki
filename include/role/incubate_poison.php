<?php
/*
  ◆潜毒者 (incubate_poison)
  ○仕様
  ・毒：人狼系 + 妖狐陣営 (5日目以降)
*/
class Role_incubate_poison extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 4) OutputAbilityResult('ability_poison', NULL); //能力発現
  }

  function FilterPoisonTarget(&$list){
    global $USERS;

    $stack = array();
    foreach($list as $uname){
      if($USERS->ByRealUname($uname)->IsRoleGroup('wolf', 'fox')) $stack[] = $uname;
    }
    $list = $stack;
  }
}
