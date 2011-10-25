<?php
/*
  ◆共鳴者 (mind_friend)
  ○仕様
  ・仲間表示：共鳴先
  ・発言透過：共鳴先
*/
class Role_mind_friend extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;
    if($ROOM->date > 1) parent::OutputAbility();
  }

  protected function OutputPartner(){
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
