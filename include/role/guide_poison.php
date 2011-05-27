<?php
/*
  ◆誘毒者
  ○仕様
  ・毒：毒能力者
  ・襲撃毒死回避：毒能力者以外
*/
class Role_guide_poison extends Role{
  function __construct(){ parent::__construct(); }

  function FilterPoisonTarget(&$list){
    global $USERS;

    $stack = array();
    foreach($list as $uname){
      if($USERS->ByRealUname($uname)->IsRoleGroup('poison')) $stack[] = $uname;
    }
    $list = $stack;
  }

  function AvoidPoisonEat($user){ return ! $user->IsRoleGroup('poison'); }
}
