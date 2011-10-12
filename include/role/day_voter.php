<?php
/*
  ◆一日村長 (day_voter)
  ○仕様
  ・表示された日のみ、投票数が +1 される
*/
class Role_day_voter extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    if($this->GetActor()->IsDoomRole($this->role)) parent::OutputAbility();
  }

  function FilterVoteDo(&$vote_number){
    global $ROOM;
    if($this->GetActor()->GetDoomDate($this->role) == $ROOM->date) $vote_number++;
  }
}
