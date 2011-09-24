<?php
/*
  ◆紅天使 (scarlet_angel)
  ○仕様
  ・特殊表示：無意識枠
  ・共感者判定：常時有効
*/
RoleManager::LoadFile('angel');
class Role_scarlet_angel extends Role_angel{
  function __construct(){ parent::__construct(); }

  function OutputCupidAbility(){
    global $ROOM, $USERS;

    if(! $ROOM->IsNight()) return;
    $stack = array();
    foreach($USERS->rows as $user){
      if($this->IsSameUser($user->uname) || $user->IsWolf()) continue;
      if($user->IsRole('unconscious') || $user->IsRoleGroup('scarlet')){
	$stack[] = $user->handle_name;
      }
    }
    OutputPartner($stack, 'unconscious_list');
  }

  function IsSympathy($lovers_a, $lovers_b){ return true; }
}
