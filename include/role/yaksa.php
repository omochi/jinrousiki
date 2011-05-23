<?php
/*
  ◆夜叉
  ○仕様
  ・勝利条件：自分自身の生存 + 人狼系の全滅
*/
class Role_yaksa extends Role{
  public $resist_rate = 20;

  function __construct(){ parent::__construct(); }

  function Ignored($user){ return ! $user->IsWolf(); }

  function GetReduceRate(){ return 1 / 5; }

  function DistinguishVictory($victory){
    global $USERS;

    if($this->IsDead() || $victory == 'wolf') return false;
    foreach($USERS->rows as $user){
      if($user->IsLiveRoleGroup('wolf')) return false;
    }
    return true;
  }
}
