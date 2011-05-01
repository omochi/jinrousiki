<?php
/*
  ◆蓬莱人形 (doom_doll)
  ○仕様
  ・処刑：死の宣告 (人形以外)
*/
class Role_doom_doll extends Role{
  function __construct(){ parent::__construct(); }

  function VoteKillCounter($voter_list){
    global $USERS;

    $stack = array(); //対象者の選出
    foreach($voter_list as $uname){
      $user = $USERS->ByRealUname($uname);
      if(! $user->IsAvoid() && ! $user->IsDoll()) $stack[] = $user->user_no;
    }
    //PrintData($stack, 'Target [death_warrant]');
    if(count($stack) > 0) $USERS->ByID(GetRandom($stack))->AddDoom(2);
  }
}
