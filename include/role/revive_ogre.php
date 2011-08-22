<?php
/*
  ◆茨木童子 (revive_ogre)
  ○仕様
  ・勝利条件：自分自身の生存 + 嘘吐きの全滅
*/
RoleManager::LoadFile('ogre');
class Role_revive_ogre extends Role_ogre{
  public $reduce_rate = 2;

  function __construct(){ parent::__construct(); }

  function GetResistRate(){ return 0; }

  function Win($victory){
    if($this->IsDead()) return false;
    foreach($this->GetUser() as $user){
      if($user->IsLive() && $user->IsLiar()) return false;
    }
    return true;
  }
}
