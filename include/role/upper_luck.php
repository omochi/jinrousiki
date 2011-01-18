<?php
/*
  ◆雑草魂 (upper_luck)
  ○仕様
  ・2日目の得票数が +4 される代わりに、3日目以降は -2 される。
*/
class Role_upper_luck extends Role{
  function __construct(){ parent::__construct(); }

  function FilterVoted(&$voted_number){
    global $ROOM;
    $voted_number += $ROOM->date == 2 ? 4 : -2;
  }
}
