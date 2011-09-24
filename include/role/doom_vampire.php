<?php
/*
  ◆冥血鬼 (doom_vampire)
  ○仕様
  ・吸血：死の宣告
  ・人狼襲撃耐性：常時無効
*/
RoleManager::LoadFile('vampire');
class Role_doom_vampire extends Role_vampire{
  function __construct(){ parent::__construct(); }

  function Infect($user){
    $user->AddRole($this->GetActor()->GetID('infected'));
    $user->AddDoom(4);
  }

  function WolfEatResist(){ return true; }
}
