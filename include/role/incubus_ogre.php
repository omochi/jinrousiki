<?php
/*
  ◆般若
  ○仕様
  ・勝利条件：自分自身の生存 + 女性の全滅
*/
class Role_incubus_ogre extends Role{
  public $resist_rate = 40;

  function __construct(){ parent::__construct(); }

  function GetReduceRate(){ return 1 / 2; }

  function Win($victory){
    global $USERS;

    if($this->IsDead()) return false;
    foreach($USERS->rows as $user){
      if(! $this->IsSameUser($user->uname) && $user->IsLive() && $user->sex == 'female'){
	return false;
      }
    }
    return true;
  }
}
