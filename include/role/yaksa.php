<?php
/*
  ◆夜叉 (yaksa)
  ○仕様
  ・勝利条件：自分自身の生存 + 人狼系の全滅
*/
class Role_yaksa extends Role{
  public $resist_rate = 20;

  function __construct(){ parent::__construct(); }

  function Ignored($user){ return ! $user->IsWolf(); }

  function GetReduceRate(){ return 1 / 5; }

  function Win($victory){
    if($victory == 'wolf' || $this->IsDead()) return false;
    foreach($this->GetUser() as $user){
      if($user->IsLive() && ! $this->Ignored($user)) return false;
    }
    return true;
  }
}
