<?php
/*
  ◆水鬼 (north_ogre)
  ○仕様
  ・勝利条件：自分自身の生存 + 自分と同列の上側にいる人の全滅 + 村人陣営の勝利
*/
RoleManager::LoadFile('ogre');
class Role_north_ogre extends Role_ogre{
  public $resist_rate = 40;
  public $reduce_rate = 2;
  function __construct(){ parent::__construct(); }

  function Win($victory){
    if($victory != 'human' || $this->IsDead()) return false;
    $id = $this->GetActor()->user_no;
    foreach($this->GetUser() as $user){
      if($user->user_no >= $id) return true;
      if($user->user_no % 5 == $id % 5 && $user->IsLive()) return false;
    }
    return true;
  }
}
