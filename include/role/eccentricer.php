<?php
/*
  ◆傾奇者 (eccentricer)
  ○仕様
  ・投票数：+1 (4日目まで)
*/
class Role_eccentricer extends Role {
  function __construct(){ parent::__construct(); }

  function OutputResult(){ if ($this->IsLost()) OutputAbilityResult('ability_eccentricer', null); }

  function FilterVoteDo(&$number){ if (! $this->IsLost()) $number++; }

  private function IsLost(){ return DB::$ROOM->date > 4; }
}
