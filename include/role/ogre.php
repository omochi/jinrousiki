<?php
/*
  ◆鬼 (ogre)
  ○仕様
  ・勝利条件：自分自身と人狼系の生存
*/
class Role_ogre extends Role{
  public $resist_rate = 30;

  function __construct(){ parent::__construct(); }

  function GetReduceRate(){ return 1 / 5; }

  function Win($victory){
    if($this->IsDead()) return false;
    if($victory == 'wolf') return true;
    foreach($this->GetUser() as $user){
      if($user->IsLiveRoleGroup('wolf')) return true;
    }
    return false;
  }
}
