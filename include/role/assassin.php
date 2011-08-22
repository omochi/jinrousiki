<?php
/*
  ◆暗殺者 (assassin)
  ○仕様
  ・暗殺：標準
*/
class Role_assassin extends Role{
  function __construct(){ parent::__construct(); }

  function Assassin($user, &$list, &$reverse){
    if($user->IsLive(true)) $list[$user->uname] = true;
  }
}
