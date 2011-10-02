<?php
/*
  ◆呪術師 (voodoo_mad)
  ○仕様
*/
class Role_voodoo_mad extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'voodoo_do', 'VOODOO_MAD_DO');
  }

  //呪術対象セット
  function SetVoodoo($user){
    global $ROOM, $USERS, $ROLES;

    $actor = $this->GetActor();
    if(! $ROOM->IsEvent('no_cursed') && $user->IsLiveRoleGroup('cursed')){ //呪返し判定
      foreach($ROLES->LoadFilter('anti_voodoo') as $filter){ //厄神の護衛判定
	if($filter->GuardCurse($actor)) return;
      }
    }
    if(in_array($user->uname, $this->GetStack('voodoo_killer'))) //陰陽師の解呪判定
      $this->AddSuccess($user->uname, 'voodoo_killer_success');
    else
      $this->AddStack($user->uname, 'voodoo');
  }

  //呪術能力者の呪返し処理
  function VoodooToVoodoo(){
    global $USERS, $ROLES;

    $stack = $this->GetStack('voodoo');
    $count_list  = array_count_values($stack);
    $filter_list = $ROLES->LoadFilter('anti_voodoo');
    foreach($stack as $uname => $target_uname){
      if($count_list[$target_uname] > 1){
	//厄神の護衛判定
	foreach($filter_list as $filter) $filter->GuardCurse($USERS->ByUname($uname));
      }
    }
  }
}
