<?php
/*
  ◆死神 (doom_assassin)
  ○仕様
  ・暗殺：死の宣告 (2日後)
*/
class Role_doom_assassin extends Role{
  function __construct(){ parent::__construct(); }

  function Assassin($user, &$list, &$reverse){
    if($user->IsLive(true)) $user->AddDoom(2, 'death_warrant');
  }
}
