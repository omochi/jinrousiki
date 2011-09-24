<?php
/*
  ◆狂信者 (fanatic_mad)
  ○仕様
*/
class Role_fanatic_mad extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $USERS;

    parent::OutputAbility();
    $stack = array();
    foreach($USERS->rows as $user){
      if($user->IsRole('possessed_wolf')){
	$stack[] = $USERS->GetHandleName($user->uname, true); //憑依先を追跡する
      }
      elseif($user->IsWolf(true)){
	$stack[] = $user->handle_name;
      }
    }
    OutputPartner($stack, 'wolf_partner');
  }
}
