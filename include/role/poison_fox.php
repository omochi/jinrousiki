<?php
/*
  ◆管狐 (poison_fox)
  ○仕様
  ・人狼襲撃耐性：無し
  ・毒：妖狐陣営以外
*/
RoleManager::LoadFile('fox');
class Role_poison_fox extends Role_fox{
  public $resist_wolf = false;
  function __construct(){ parent::__construct(); }

  function FilterPoisonTarget(&$list){
    global $USERS;

    $stack = array();
    foreach($list as $uname){
      if(! $USERS->ByRealUname($uname)->IsFox()) $stack[] = $uname;
    }
    $list = $stack;
  }
}
