<?php
/*
  ◆夢毒者 (dummy_poison)
  ○仕様
  ・毒：獏・妖精系
*/
class Role_dummy_poison extends Role{
  public $display_role = 'poison';
  function __construct(){ parent::__construct(); }

  function FilterPoisonTarget(&$list){
    global $USERS;

    $stack = array();
    foreach($list as $uname){
      $user = $USERS->ByRealUname($uname);
      if($user->IsRole('dream_eater_mad') || $user->IsRoleGroup('fairy')) $stack[] = $uname;
    }
    $list = $stack;
  }
}
