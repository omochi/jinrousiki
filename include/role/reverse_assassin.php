<?php
/*
  ◆反魂師 (reverse_assassin)
  ○仕様
  ・暗殺：反魂
*/
RoleManager::LoadFile('assassin');
class Role_reverse_assassin extends Role_assassin{
  function __construct(){ parent::__construct(); }

  function Assassin($user){
    global $ROLES;
    $ROLES->stack->reverse_assassin[$this->GetActor()->uname] = $user->uname;
  }

  function AssassinKill(){
    global $USERS, $ROLES;

    foreach($this->GetStack() as $uname => $target_uname){
      $target = $USERS->ByUname($target_uname);
      if($target->IsLive(true))
	$USERS->Kill($target->user_no, 'ASSASSIN_KILLED');
      elseif(! $target->IsLovers())
	$ROLES->stack->reverse[$target_uname] = ! $ROLES->stack->reverse[$target_uname];
    }
  }
}
