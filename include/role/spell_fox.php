<?php
/*
  ◆宙狐
  ○仕様
  ・人狼襲撃カウンター：狐火 (一回限定)
*/
class Role_spell_fox extends Role{
  function __construct(){ parent::__construct(); }

  function FoxEatCounter($user){
    if(! $this->GetActor()->IsActive()) return false;
    $user->AddRole('spell_wisp');
    $this->GetActor()->LostAbility();
  }
}
