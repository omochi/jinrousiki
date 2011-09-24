<?php
/*
  ◆月狐 (jammer_fox)
  ○仕様
*/
RoleManager::LoadFile('child_fox');
class Role_jammer_fox extends Role_child_fox{
  function __construct(){ parent::__construct(); }

  function OutputFoxAbility(){
    global $ROOM;
    if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'jammer_do', 'JAMMER_MAD_DO');
  }
}
