<?php
/*
  ◆共鳴者 (mind_friend)
  ○仕様
*/
class Role_mind_friend extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    if($ROOM->date < 2) return;
    parent::OutputAbility();
    $target = $this->GetActor()->partner_list;
    $stack  = array();
    foreach($this->GetUser() as $user){
      if($this->IsActor($user->uname)) continue;
      if($user->IsPartner($this->role, $target)) $stack[$user->user_no] = $user->handle_name;
    }
    ksort($stack);
    OutputPartner($stack, $this->role . '_list');
  }

  function IsMindRead(){
    return $this->GetTalkFlag('mind_read') &&
      $this->GetActor()->IsPartner($this->role, $this->GetViewer()->partner_list);
  }
}
