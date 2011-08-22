<?php
/*
  ◆冥狐 (doom_fox)
  ○仕様
  ・暗殺：死の宣告(4日後)
*/
class Role_doom_fox extends Role{
  function __construct(){ parent::__construct(); }

  function Assassin($user, &$list, &$reverse){
    if($user->IsLive(true)) $user->AddDoom(4, 'death_warrant');
  }
}
