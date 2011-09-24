<?php
/*
  ◆反魂師 (reverse_assassin)
  ○仕様
  ・暗殺：反魂
*/
RoleManager::LoadFile('assassin');
class Role_reverse_assassin extends Role_assassin{
  function __construct(){ parent::__construct(); }

  function Assassin($user, &$list, &$reverse){ $reverse[$this->GetActor()->uname] = $user->uname; }
}
