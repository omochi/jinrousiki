<?php
/*
  ◆毒狼 (poison_wolf)
  ○仕様
  ・毒：人狼系以外
  ・襲撃毒死回避：人狼系
*/
RoleManager::LoadFile('wolf');
class Role_poison_wolf extends Role_wolf{
  function __construct(){ parent::__construct(); }

  function FilterPoisonTarget(&$list){
    global $USERS;

    $stack = array();
    foreach($list as $uname){
      if(! $USERS->ByRealUname($uname)->IsWolf()) $stack[] = $uname;
    }
    $list = $stack;
  }

  function AvoidPoisonEat($user){ return $user->IsWolf(); }
}
