<?php
/*
  ◆般若 (incubus_ogre)
  ○仕様
  ・勝利条件：自分自身の生存 + 女性の全滅
*/
RoleManager::LoadFile('ogre');
class Role_incubus_ogre extends Role_ogre{
  public $resist_rate = 40;
  public $reduce_rate = 2;

  function __construct(){ parent::__construct(); }

  function Win($victory){
    if($this->IsDead()) return false;
    foreach($this->GetUser() as $user){
      if(! $this->IsSameUser($user->uname) && $user->IsLive() && $user->IsFemale()) return false;
    }
    return true;
  }
}
