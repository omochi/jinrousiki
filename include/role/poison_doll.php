<?php
/*
  ◆鈴蘭人形 (poison_doll)
  ○仕様
  ・毒：上海人形系以外 (人形遣いは毒対象)
*/
RoleManager::LoadFile('doll');
class Role_poison_doll extends Role_doll{
  function __construct(){ parent::__construct(); }

  function FilterPoisonTarget(&$list){
    global $USERS;

    $stack = array();
    foreach($list as $uname){
      if(! $USERS->ByRealUname($uname)->IsDoll()) $stack[] = $uname;
    }
    $list = $stack;
  }
}
