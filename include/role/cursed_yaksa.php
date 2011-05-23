<?php
/*
  ◆滝夜叉姫
  ○仕様
  ・勝利条件：自分自身の生存 + 占い師系・魔法使い系の全滅
*/
class Role_cursed_yaksa extends Role{
  public $resist_rate = 20;

  function __construct(){ parent::__construct(); }

  function Ignored($user){
    return ! ($user->IsRoleGroup('mage', 'wizard') || $user->IsRole('voodoo_killer'));
  }

  function GetReduceRate(){ return 1 / 3; }

  function DistinguishVictory($victory){
    global $USERS;

    if($this->IsDead()) return false;
    foreach($USERS->rows as $user){
      if($user->IsLiveRoleGroup('mage', 'wizard') || $user->IsLiveRole('voodoo_killer')){
	return false;
      }
    }
    return true;
  }
}
