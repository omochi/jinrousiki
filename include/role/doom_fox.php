<?php
/*
  ◆冥狐 (doom_fox)
  ○仕様
  ・暗殺：死の宣告(4日後)
*/
RoleManager::LoadFile('fox');
class Role_doom_fox extends Role_fox{
  function __construct(){ parent::__construct(); }

  function OutputFoxAbility(){
    global $ROOM;

    if($ROOM->date > 1 && $ROOM->IsNight()){
      OutputVoteMessage('assassin-do', 'assassin_do', 'ASSASSIN_DO', 'ASSASSIN_NOT_DO');
    }
  }

  function Assassin($user, &$list, &$reverse){
    if($user->IsLive(true)) $user->AddDoom(4, 'death_warrant');
  }
}
