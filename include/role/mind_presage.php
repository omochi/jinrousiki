<?php
/*
  ◆受託者 (mind_presage)
  ○仕様
*/
class Role_mind_presage extends Role {
  function __construct(){ parent::__construct(); }

  protected function IgnoreAbility(){ return DB::$ROOM->date < 3; }

  protected function OutputImage(){ return; }

  protected function OutputResult(){ OutputSelfAbilityResult('PRESAGE_RESULT'); }
}
