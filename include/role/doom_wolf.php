<?php
/*
  ◆冥狼
  ○仕様
  ・妖狐襲撃：死の宣告
  ・襲撃：死の宣告
*/
class Role_doom_wolf extends Role{
  function __construct(){ parent::__construct(); }

  function FoxEatAction($user){ $user->AddDoom(2); }

  function WolfEatAction($user){
    $user->AddDoom(2);
    $user->wolf_killed = true; //尾行判定は成功扱い
    return true;
  }
}
