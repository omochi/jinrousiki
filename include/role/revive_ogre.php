<?php
/*
  ◆茨木童子
  ○仕様
  ・勝利条件：自分自身の生存 + 嘘吐きの全滅
*/
class Role_revive_ogre extends Role{
  public $resist_rate = 0;

  function __construct(){ parent::__construct(); }

  function GetReduceRate(){ return 1 / 2; }

  function Win($victory){
    global $USERS;

    if($this->IsDead()) return false;
    foreach($USERS->rows as $user){
      if($user->IsLive() && $user->IsLiar()) return false;
    }
    return true;
  }
}
