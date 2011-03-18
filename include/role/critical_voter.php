<?php
/*
  ◆会心 (critical_voter)
  ○仕様
  ・5% の確率 or 天候「烈日」で投票数が +100 される
*/
class Role_critical_voter extends Role{
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    global $ROOM;
    if($ROOM->IsEvent('critical') || mt_rand(1, 100) <= 5) $vote_number += 100;
  }
}
