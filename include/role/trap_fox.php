<?php
/*
  ◆狡狐 (trap_fox)
  ○仕様
*/
RoleManager::LoadFile('fox');
class Role_trap_fox extends Role_fox{
  function __construct(){ parent::__construct(); }

  function OutputFoxAbility(){
    global $ROOM;

    if($ROOM->date > 1 && $ROOM->IsNight() && $this->GetActor()->IsActive()){
      OutputVoteMessage('wolf-eat', 'trap_do', 'TRAP_MAD_DO', 'TRAP_MAD_NOT_DO');
    }
  }
}
