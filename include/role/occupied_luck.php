<?php
/*
  ◆ひんな持ち (occupied_luck)
  ○仕様
  ・得票数：+1 (付加者生存) / +3 (付加者全滅)
*/
class Role_occupied_luck extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;
    if($ROOM->date > 1) parent::OutputAbility();
  }

  function FilterVoted(&$number){ $number += $this->IsLivePartner() ? 1 : 3; }
}
