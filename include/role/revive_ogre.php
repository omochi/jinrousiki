<?php
/*
  ◆茨木童子 (revive_ogre)
  ○仕様
  ・勝利：生存 + 嘘吐き全滅
  ・人狼襲撃：確率蘇生
*/
RoleManager::LoadFile('ogre');
class Role_revive_ogre extends Role_ogre{
  public $mix_in = 'revive_pharmacist';
  public $reduce_rate = 2;
  function __construct(){ parent::__construct(); }

  function Win($victory){
    if($this->IsDead()) return false;
    foreach($this->GetUser() as $user){
      if($user->IsLive() && $user->IsLiar()) return false;
    }
    return true;
  }

  function GetResistRate(){ return 0; }

  function Resurrect(){
    global $ROOM;
    if($this->IsResurrect() && ! $ROOM->IsEvent('seal_ogre') &&
       ($ROOM->IsEvent('full_ogre') || mt_rand(1, 100) <= 40)){
      $this->GetActor()->Revive();
    }
  }
}
