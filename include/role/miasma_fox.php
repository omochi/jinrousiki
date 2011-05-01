<?php
/*
  ◆蟲狐 (miasma_fox)
  ○仕様
  ・処刑：熱病 (妖狐陣営以外)
  ・人狼襲撃：熱病
*/
class Role_miasma_fox extends Role{
  function __construct(){ parent::__construct(); }

  function VoteKillCounter($voter_list){
    global $USERS;

    $stack = array(); //対象者の選出
    foreach($voter_list as $uname){
      $user = $USERS->ByRealUname($uname);
      if(! $user->IsAvoid() && ! $user->IsFox()) $stack[] = $user->user_no;
    }
    //PrintData($stack, 'Target [febris]');
    if(count($stack) > 0) $USERS->ByID(GetRandom($stack))->AddDoom(1, 'febris');
  }

  function WolfEatCounter($user){ $user->AddDoom(1, 'febris'); }
}
