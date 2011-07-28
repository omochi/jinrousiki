<?php
/*
  ◆猟師 (hunter_guard)
  ○仕様
  ・護衛失敗：通常
  ・護衛処理：死亡 (人狼襲撃限定)
  ・狩り：通常 + 妖狐陣営
*/
class Role_hunter_guard extends Role{
  function __construct(){ parent::__construct(); }

  function GuardFailed(){ return false; }

  function GuardAction($user, $flag = false){
    global $USERS;
    if(! $flag) $USERS->Kill($this->GetActor()->user_no, 'WOLF_KILLED');
  }

  function IsHuntTarget($user){ return $user->IsHuntTarget() || $user->IsFox(); }
}
