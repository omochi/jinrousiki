<?php
/*
  ◆座敷童子 (brownie)
  ○仕様
  ・処刑：熱病
*/
class Role_brownie extends Role {
  function __construct(){ parent::__construct(); }

  function VoteKillCounter($list){
    $stack = array();
    foreach ($list as $uname) {
      $user = DB::$USER->ByRealUname($uname);
      if (! $user->IsAvoid()) $stack[] = $user->user_no;
    }
    if (count($stack) > 0) DB::$USER->ByID(GetRandom($stack))->AddDoom(1, 'febris');
  }
}
