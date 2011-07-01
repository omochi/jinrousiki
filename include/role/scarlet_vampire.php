<?php
/*
  ◆屍鬼 (scarlet_vampire)
  ○仕様
  ・吸血：通常
*/
class Role_scarlet_vampire extends Role{
  function __construct(){ parent::__construct(); }

  function Infect($user){ $user->AddRole($this->GetActor()->GetID('infected')); }
}
