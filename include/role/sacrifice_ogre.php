<?php
/*
  ◆酒呑童子 (sacrifice_ogre)
  ○仕様
  ・勝利条件：自分自身の生存 + 村人陣営以外の勝利
  ・人攫い無効：吸血鬼陣営
  ・人攫い：洗脳者付加
  ・身代わり対象者：洗脳者
*/
RoleManager::LoadFile('ogre');
class Role_sacrifice_ogre extends Role_ogre{
  public $reduce_rate = 2;
  function __construct(){ parent::__construct(); }

  function OutputOgreAbility(){
    global $ROOM, $USERS;

    if($ROOM->date < 1) return;
    $stack = array();
    foreach($USERS->rows as $user){
      if($user->IsRole('psycho_infected')) $stack[] = $user->handle_name;
    }
    OutputPartner($stack, 'psycho_infected_list');
  }

  function GetResistRate(){ return 0; }

  function Ignored($user){ return $user->IsCamp('vampire'); }

  function AssassinAction($user){ $user->AddRole('psycho_infected'); }

  function Win($victory){ return $victory != 'human' && $this->IsLive(); }

  function GetSacrificeList(){
    $stack = array();
    foreach($this->GetUser() as $user){
      if(! $this->IsSameUser($user->uname) && $user->IsLiveRole('psycho_infected', true)){
	$stack[] = $user->user_no;
      }
    }
    return $stack;
  }
}
