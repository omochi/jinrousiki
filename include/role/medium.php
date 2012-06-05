<?php
/*
  ◆巫女 (medium)
  ○仕様
*/
class Role_medium extends Role {
  function __construct(){ parent::__construct(); }

  protected function OutputResult(){
    if (DB::$ROOM->date > 1) $this->OutputAbilityResult('MEDIUM_RESULT');
  }
}
