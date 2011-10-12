<?php
/*
  ◆酒呑童子 (sacrifice_ogre)
  ○仕様
  ・勝利：生存 + 村人陣営以外勝利
  ・人攫い無効：吸血鬼陣営
  ・人攫い：洗脳者付加
  ・身代わり対象者：洗脳者
*/
RoleManager::LoadFile('ogre');
class Role_sacrifice_ogre extends Role_ogre{
  public $mix_in = 'protected';
  public $reduce_rate = 2;
  function __construct(){ parent::__construct(); }

  function OutputOgreAbility(){
    global $ROOM;

    if($ROOM->date < 1) return;
    $stack = array();
    foreach($this->GetUser() as $user){
      if($user->IsRole('psycho_infected')) $stack[] = $user->handle_name;
    }
    OutputPartner($stack, 'psycho_infected_list');
  }

  function Win($victory){ return $victory != 'human' && $this->IsLive(); }

  function GetResistRate(){ return 0; }

  function IgnoreAssassin($user){ return $user->IsCamp('vampire'); }

  function Assassin($user){ $user->AddRole('psycho_infected'); }

  function IsSacrifice($user){
    return ! $this->IsActor($user->uname) && $user->IsRole('psycho_infected');
  }
}
