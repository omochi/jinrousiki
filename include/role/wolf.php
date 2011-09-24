<?php
/*
  ◆人狼 (wolf)
  ○仕様
*/
class Role_wolf extends Role{
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM, $USERS;

    parent::OutputAbility();
    //仲間情報を収集
    $stack = array();
    foreach($USERS->rows as $user){
      if($this->IsSameUser($user->uname)) continue;
      if($user->IsRole('possessed_wolf')){
	$stack['wolf'][] = $USERS->GetHandleName($user->uname, true); //憑依先を追跡する
      }
      elseif($user->IsWolf(true)){
	$stack['wolf'][] = $user->handle_name;
      }
      elseif($user->IsRole('whisper_mad')){
	$stack['mad'][] = $user->handle_name;
      }
      elseif($user->IsRole('unconscious') || $user->IsRoleGroup('scarlet')){
	$stack['unconscious'][] = $user->handle_name;
      }
    }
    if($this->GetActor()->IsWolf(true)){
      OutputPartner($stack['wolf'], 'wolf_partner'); //人狼
      OutputPartner($stack['mad'], 'mad_partner'); //囁き狂人
    }
    if($ROOM->IsNight()) OutputPartner($stack['unconscious'], 'unconscious_list'); //無意識
    $this->OutputWolfAbility();
    if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'wolf_eat', 'WOLF_EAT'); //投票
  }

  //特殊狼の情報表示
  function OutputWolfAbility(){}
}
