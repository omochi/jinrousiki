<?php
/*
  ◆口寄せ (mind_evoke)
  ○仕様
*/
class Role_mind_evoke extends Role {
  function __construct(){ parent::__construct(); }

  protected function IgnoreAbility(){ return DB::$ROOM->date < 2; }
}
