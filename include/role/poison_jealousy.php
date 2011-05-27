<?php
/*
  ◆毒橋姫
  ○仕様
  ・毒：恋人
  ・襲撃毒死回避：恋人以外
*/
class Role_poison_jealousy extends Role{
  function __construct(){ parent::__construct(); }

  function FilterPoisonTarget(&$list){
    global $USERS;

    $stack = array();
    foreach($list as $uname){
      if($USERS->ByRealUname($uname)->IsLovers()) $stack[] = $uname;
    }
    $list = $stack;
  }

  function AvoidPoisonEat($user){ return ! $user->IsLovers(); }
}
