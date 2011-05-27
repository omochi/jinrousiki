<?php
/*
  ◆吸血鬼 (vampire)
  ○仕様
  ・吸血：通常
*/
class Role_vampire extends Role{
  function __construct(){ parent::__construct(); }

  function Infect($user){ $user->AddRole($this->GetActor()->GetID('infected')); }
}
