<?php
/*
  ◆妖狐 (fox)
  ○仕様
*/
class Role_fox extends Role{
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM, $USERS;

    parent::OutputAbility();
    if(! $this->GetActor()->IsLonely()){ //仲間表示
      $fox_list       = array();
      $child_fox_list = array();
      foreach($this->GetUser() as $user){
	if($this->IsSameUser($user->uname)) continue;
	if($user->IsRole('possessed_fox')){
	  $fox_list[] = $USERS->GetHandleName($user->uname, true); //憑依先を追跡する
	}
	elseif($user->IsFox(true)){
	  $fox_list[] = $user->handle_name;
	}
	elseif($user->IsChildFox() || $user->IsRoleGroup('scarlet')){
	  $child_fox_list[] = $user->handle_name;
	}
      }
      OutputPartner($fox_list, 'fox_partner'); //妖狐系
      OutputPartner($child_fox_list, 'child_fox_partner'); //子狐系
    }
    if($ROOM->date > 1 && ! $ROOM->IsOption('seal_message') && $this->GetActor()->IsResistFox()){
      OutputSelfAbilityResult('FOX_EAT'); //人狼襲撃
    }
    $this->OutputFoxAbility();
  }

  //特殊妖狐の情報表示
  function OutputFoxAbility(){}

  //人狼襲撃カウンター
  function FoxEatCounter($user){}
}
