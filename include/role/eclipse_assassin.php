<?php
/*
  ◆蝕暗殺者 (eclipse_assassin)
  ○仕様
  ・暗殺：確率反射
*/
class Role_eclipse_assassin extends Role{
  function __construct(){ parent::__construct(); }

  function Assassin($user, &$list, &$reverse){
    global $ROOM;

    if($user->IsDead(true)) return;
    $uname = $ROOM->IsEvent('no_reflect_assassin') || mt_rand(1, 10) > 3 ?
      $user->uname : $this->GetActor()->uname;
    $list[$uname] = true;
  }
}
