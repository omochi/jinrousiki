<?php
/*
  ◆前鬼 (orange_ogre)
  ○仕様
  ・勝利条件：自分自身の生存 + 人狼陣営の全滅
*/
class Role_orange_ogre extends Role{
  public $resist_rate = 30;

  function __construct(){ parent::__construct(); }

  function GetReduceRate(){ return 1 / 5; }

  function Win($victory){
    if($victory == 'wolf' || $this->IsDead()) return false;
    foreach($this->GetUser() as $user){
      if($user->IsLive() && $user->IsCamp('wolf', true)) return false;
    }
    return true;
  }
}
