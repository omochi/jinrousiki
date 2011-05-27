<?php
/*
  ◆冥血鬼 (doom_vampire)
  ○仕様
  ・人狼襲撃耐性：常時無効
  ・吸血：死の宣告
*/
class Role_doom_vampire extends Role{
  function __construct(){ parent::__construct(); }

  function WolfEatResist(){ return true; }

  function Infect($user){
    $user->AddRole($this->GetActor()->GetID('infected'));
    $user->AddDoom(4);
  }
}
