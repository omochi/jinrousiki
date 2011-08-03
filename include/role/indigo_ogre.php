<?php
/*
  ◆後鬼
  ○仕様
  ・勝利条件：自分自身の生存 + 妖狐陣営の全滅
*/
class Role_indigo_ogre extends Role{
  public $resist_rate = 30;

  function __construct(){ parent::__construct(); }

  function GetReduceRate(){ return 1 / 5; }

  function Win($victory){
    global $USERS;

    if($this->IsDead() || strpos($victory, 'fox') !== false) return false;
    foreach($USERS->rows as $user){
      if($user->IsLive() && $user->IsCamp('fox', true)) return false;
    }
    return true;
  }
}
