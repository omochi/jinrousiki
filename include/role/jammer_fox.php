<?php
/*
  ◆月狐 (jammer_fox)
  ○仕様
  ・占い妨害：70%
*/
RoleManager::LoadFile('child_fox');
class Role_jammer_fox extends Role_child_fox{
  public $mix_in = 'jammer_mad';
  function __construct(){ parent::__construct(); }

  function OutputFoxAbility(){
    global $ROOM;
    if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'jammer_do', 'JAMMER_MAD_DO');
  }

  function SetJammer($user){
    if($this->IsJammer($user) && mt_rand(1, 10) < 7) $this->AddStack($user->uname, 'jammer');
  }
}
