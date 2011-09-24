<?php
/*
  ◆強毒者 (strong_poison)
  ○仕様
  ・毒：人狼系 + 妖狐陣営
*/
class Role_strong_poison extends Role{
  public $display_role = 'poison';
  function __construct(){ parent::__construct(); }

  function FilterPoisonTarget(&$list){
    global $USERS;

    $stack = array();
    foreach($list as $uname){
      if($USERS->ByRealUname($uname)->IsRoleGroup('wolf', 'fox')) $stack[] = $uname;
    }
    $list = $stack;
  }
}
