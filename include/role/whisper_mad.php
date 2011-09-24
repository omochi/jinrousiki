<?php
/*
  ◆囁き狂人 (whisper_mad)
  ○仕様
*/
class Role_whisper_mad extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $USERS;

    parent::OutputAbility();
    $stack = array();
    foreach($USERS->rows as $user){
      if($user->IsSelf()) continue;
      if($user->IsRole('possessed_wolf')){
	$stack['wolf'][] = $USERS->GetHandleName($user->uname, true); //憑依先を追跡する
      }
      elseif($user->IsWolf(true)){
	$stack['wolf'][] = $user->handle_name;
      }
      elseif($user->IsRole('whisper_mad')){
	$stack['mad'][] = $user->handle_name;
      }
    }
    OutputPartner($stack['wolf'], 'wolf_partner');
    OutputPartner($stack['mad'], 'mad_partner');
  }
}
