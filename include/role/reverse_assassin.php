<?php
/*
  ◆反魂師 (reverse_assassin)
  ○仕様
  ・暗殺：反魂
*/
class Role_reverse_assassin extends Role{
  function __construct(){ parent::__construct(); }

  function Assassin($user, &$list, &$reverse){ $reverse[$this->GetActor()->uname] = $user->uname; }
}
