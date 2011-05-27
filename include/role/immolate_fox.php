<?php
/*
  ◆野狐禅
  ○仕様
  ・人狼襲撃カウンター：能力発現
*/
class Role_immolate_fox extends Role{
  function __construct(){ parent::__construct(); }

  function FoxEatCounter($user){ $this->GetActor()->AddRole('muster_ability'); }
}
