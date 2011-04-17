<?php
/*
  ◆夜叉丸
  ○仕様
  ・勝利条件：自分自身の生存 + 蝙蝠陣営の全滅 + 村人陣営の勝利
*/
class Role_betray_yaksa extends Role{
  var $resist_rate = 20;

  function __construct(){ parent::__construct(); }

  function Ignored($user){ return ! $user->IsCamp('chiroptera', true); }

  function GetReduceRate(){ return 1 / 5; }

  function DistinguishVictory($victory){
    global $USERS;

    if($this->IsDead() || $victory != 'human') return false;
    foreach($USERS->rows as $user){
      if($user->IsLive() && $user->IsCamp('chiroptera', true)) return false;
    }
    return true;
  }
}
