<?php
/*
  ◆入道 (wirepuller_luck)
  ○仕様
  ・投票数：コピー元が誰か生きていれば +2
  ・得票数：コピー元が全員死ぬと +3
*/
class Role_wirepuller_luck extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;
    if($ROOM->date > 1) parent::OutputAbility();
  }

  function FilterVoteDo(&$vote_number){
    if($this->IsLivePartner()) $vote_number += 2;
  }

  function FilterVoted(&$voted_number){
    if(! $this->IsLivePartner()) $voted_number += 3;
  }
}
