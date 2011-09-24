<?php
/*
  ◆翠狐 (emerald_fox)
  ○仕様
*/
RoleManager::LoadFile('fox');
class Role_emerald_fox extends Role_fox{
  function __construct(){ parent::__construct(); }

  function OutputFoxAbility(){
    global $ROOM;

    if($ROOM->IsNight() && $this->GetActor()->IsActive()){
      OutputVoteMessage('mage-do', 'mage_do', 'MAGE_DO');
    }
  }
}
