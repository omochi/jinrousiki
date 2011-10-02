<?php
/*
  ◆九尾 (voodoo_fox)
  ○仕様
*/
RoleManager::LoadFile('fox');
class Role_voodoo_fox extends Role_fox{
  public $mix_in = 'voodoo_mad';
  function __construct(){ parent::__construct(); }

  function OutputFoxAbility(){
    global $ROOM;
    if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'voodoo_do', 'VOODOO_FOX_DO');
  }
}
