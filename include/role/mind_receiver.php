<?php
/*
  ◆受信者 (mind_receiver)
  ○仕様
*/
class Role_mind_receiver extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM, $USERS;

    if($ROOM->date < 2) return;
    parent::OutputAbility();
    $stack = array();
    foreach($this->GetActor()->GetPartner($this->role, true) as $id){
      $stack[$id] = $USERS->ById($id)->handle_name;
    }
    ksort($stack);
    OutputPartner($stack, 'mind_scanner_target');
  }

  function IsMindReadActive($user){
    return $this->GetTalkFlag('mind_read') &&
      $this->GetActor()->IsPartner($this->role, $user->user_no);
  }
}
