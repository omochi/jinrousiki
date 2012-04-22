<?php
/*
  ◆一発屋 (downer_luck)
  ○仕様
  ・得票数：-4 (2日目) / +2 (3日目以降)
*/
class Role_downer_luck extends Role {
  function __construct(){ parent::__construct(); }

  function FilterVotePoll(&$number){
    $number += DB::$ROOM->date == 2 ? -4 : 2;
  }
}
