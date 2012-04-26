<?php
/*
  ◆蟲狐 (miasma_fox)
  ○仕様
  ・処刑：熱病 (妖狐陣営以外)
  ・人狼襲撃：熱病
*/
RoleManager::LoadFile('child_fox');
class Role_miasma_fox extends Role_child_fox {
  public $action = null;
  public $result = null;
  function __construct(){ parent::__construct(); }

  function VoteKillCounter($list){
    $stack = array();
    foreach ($list as $uname) {
      $user = DB::$USER->ByRealUname($uname);
      if (! $user->IsAvoid() && ! $user->IsFox()) $stack[] = $user->user_no;
    }
    if (count($stack) > 0) DB::$USER->ByID(GetRandom($stack))->AddDoom(1, 'febris');
  }

  function WolfEatCounter($user){ $user->AddDoom(1, 'febris'); }
}
