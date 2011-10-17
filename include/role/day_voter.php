<?php
/*
  ◆一日村長 (day_voter)
  ○仕様
  ・投票数：+1 (当日限定)
*/
class Role_day_voter extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    if($this->GetActor()->IsDoomRole($this->role)) parent::OutputAbility();
  }

  function FilterVoteDo(&$number){
    global $ROOM;
    if($this->GetActor()->GetDoomDate($this->role) == $ROOM->date) $number++;
  }
}
