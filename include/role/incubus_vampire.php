<?php
/*
  ◆青髭公 (incubus_vampire)
  ○仕様
  ・吸血：女性以外なら吸血死
*/
class Role_incubus_vampire extends Role{
  function __construct(){ parent::__construct(); }

  function Infect($user){
    global $USERS;

    if($user->IsFemale())
      $user->AddRole($this->GetActor()->GetID('infected'));
    elseif(! $user->IsAvoid())
      $USERS->Kill($user->user_no, 'VAMPIRE_KILLED');
  }
}
