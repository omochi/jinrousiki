<?php
/*
  ◆鬼 (ogre)
  ○仕様
  ・勝利条件：自分自身と人狼系の生存
*/
class Role_ogre extends Role{
  public $resist_rate = 30;
  public $reduce_rate = 5;

  function __construct(){ parent::__construct(); }

  protected function GetEvent(){
    global $ROOM;
    return $ROOM->IsEvent('full_ogre') ? 100 : ($ROOM->IsEvent('seal_ogre') ? 0 : NULL);
  }

  function GetResistRate(){
    $event = $this->GetEvent();
    return is_null($event) ? $this->resist_rate : $event;
  }

  protected function GetReduceRate(){ return 1 / $this->reduce_rate; }

  function GetAssassinRate($times){
    $event = $this->GetEvent();
    return is_null($event) ? ceil(100 * pow($this->GetReduceRate(), $times)) : $event;
  }

  function Ignored($user){}

  function Assassin($user, &$list){ $list[$user->uname] = true; }

  function Win($victory){
    if($this->IsDead()) return false;
    if($victory == 'wolf') return true;
    foreach($this->GetUser() as $user){
      if($user->IsLiveRoleGroup('wolf')) return true;
    }
    return false;
  }
}
