<?php
/*
  ◆入道 (wirepuller_luck)
  ○仕様
  ・コピー元が生きていれば投票数が +2 される
  ・コピー元が死ぬと得票数が +3 される
*/
class Role_wirepuller_luck extends Role{
  function __construct(){ parent::__construct(); }

  function IsLivePartner(){
    global $ROLES, $USERS;

    foreach($ROLES->actor->GetPartner($this->role) as $id){
      if($USERS->ByID($id)->IsLive()) return true;
    }
    return false;
  }

  function FilterVoteDo(&$vote_number){
    if($this->IsLivePartner()) $vote_number += 2;
  }

  function FilterVoted(&$voted_number){
    if(! $this->IsLivePartner()) $voted_number += 3;
  }
}
