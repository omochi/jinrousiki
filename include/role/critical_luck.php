<?php
/*
  ◆痛恨 (critical_luck)
  ○仕様
  ・5% の確率で得票数が +100 される
*/
class Role_critical_luck extends Role{
  function __construct(){ parent::__construct(); }

  function FilterVoted(&$voted_number){
    global $ROOM;
    $vote_number += ($ROOM->IsEvent('critical') || mt_rand(1, 100) <= 5) ? 100 : 0;
  }
}
