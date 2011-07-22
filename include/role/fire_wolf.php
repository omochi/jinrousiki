<?php
/*
  ◆火狼
  ○仕様
  ・妖狐襲撃：天火 (一回限定)
  ・襲撃：天火 (一回限定)
*/
class Role_fire_wolf extends Role{
  function __construct(){ parent::__construct(); }

  function FoxEatAction($user){
    if(! $this->GetActor()->IsActive()) return false;
    $user->AddRole('black_wisp');
    $this->GetActor()->LostAbility();
  }

  function WolfEatAction($user){
    if(! $this->GetActor()->IsActive()) return false;
    $user->AddRole('black_wisp');
    $user->wolf_killed = true; //尾行判定は成功扱い
    $this->GetActor()->LostAbility();
    return true;
  }
}
