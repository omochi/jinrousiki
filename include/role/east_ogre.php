<?php
/*
  ◆風鬼 (east_ogre)
  ○仕様
  ・勝利条件：自分自身の生存 + 自分と同列の右側にいる人の全滅 + 村人陣営の勝利
*/
class Role_east_ogre extends Role{
  public $resist_rate = 40;

  function __construct(){ parent::__construct(); }

  function GetReduceRate(){ return 1 / 2; }

  function Win($victory){
    if($victory != 'human' || $this->IsDead()) return false;
    $id = $this->GetActor()->user_no;
    foreach($this->GetUser() as $user){
      if($user->user_no > ceil($id / 5) * 5) return true;
      if($user->user_no > $id && $user->IsLive()) return false;
    }
    return true;
  }
}
