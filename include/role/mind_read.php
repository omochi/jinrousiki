<?php
/*
  ◆サトラレ (mind_read)
  ○仕様
*/
class Role_mind_read extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;
    if($ROOM->date > 1) parent::OutputAbility();
  }

  function IsMindRead(){
    return $this->GetTalkFlag('mind_read') &&
      $this->GetActor()->IsPartner($this->role, $this->GetViewer()->user_no) &&
      ! $this->GetActor()->IsRole('unconscious');
  }
}
