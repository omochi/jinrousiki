<?php
/*
  ◆座敷童子 (brownie)
  ○仕様
  ・処刑されたら投票者から死の宣告を行う
*/
class Role_brownie extends Role{
  function __construct(){ parent::__construct(); }

  function VoteKillCounter($voter_list){
    global $USERS;

    $stack = array(); //対象者の選出
    foreach($voter_list as $uname){
      $user = $USERS->ByRealUname($uname);
      if(! $user->IsAvoid()) $stack[] = $user->user_no;
    }
    //PrintData($stack, 'Target [febris]');
    if(count($stack) > 0) $USERS->ByID(GetRandom($stack))->AddDoom(1, 'febris');
  }
}