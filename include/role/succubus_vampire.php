<?php
/*
  ◆飛縁魔 (succubus_vampire)
  ○仕様
  ・吸血：男性以外なら吸血死
*/
class Role_succubus_vampire extends Role{
  function __construct(){ parent::__construct(); }

  function Infect($user){
    global $USERS;

    if($user->IsMale())
      $user->AddRole($this->GetActor()->GetID('infected'));
    elseif(! $user->IsAvoid())
      $USERS->Kill($user->user_no, 'VAMPIRE_KILLED');
  }
}
