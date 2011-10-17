<?php
/*
  ◆入道 (wirepuller_luck)
  ○仕様
  ・投票数：+2 (付加者生存)
  ・得票数：+3 (付加者全滅)
*/
class Role_wirepuller_luck extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;
    if($ROOM->date > 1) parent::OutputAbility();
  }

  function FilterVoteDo(&$number){ if($this->IsLivePartner()) $number += 2; }

  function FilterVoted(&$number){ if(! $this->IsLivePartner()) $number += 3; }
}
