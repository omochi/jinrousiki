<?php
/*
  ◆般若 (incubus_ogre)
  ○仕様
  ・勝利条件：自分自身の生存 + 女性の全滅
*/
class Role_incubus_ogre extends Role{
  public $resist_rate = 40;

  function __construct(){ parent::__construct(); }

  function GetReduceRate(){ return 1 / 2; }

  function Win($victory){
    if($this->IsDead()) return false;
    foreach($this->GetUser() as $user){
      if(! $this->IsSameUser($user->uname) && $user->IsLive() && $user->IsFemale()) return false;
    }
    return true;
  }
}
