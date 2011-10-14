<?php
/*
  ◆猟師 (hunter_guard)
  ○仕様
  ・護衛処理：死亡 (人狼襲撃限定)
  ・狩り：通常 + 妖狐陣営
*/
RoleManager::LoadFile('guard');
class Role_hunter_guard extends Role_guard{
  function __construct(){ parent::__construct(); }

  function GuardAction($user, $flag = false){
    global $USERS;
    if(! $flag) $USERS->Kill($this->GetActor()->user_no, 'WOLF_KILLED');
  }

  function IsHunt($user){ return $this->IsHuntTarget($user) || $user->IsFox(); }
}
