<?php
/*
  ◆呪術師 (voodoo_mad)
  ○仕様
*/
class Role_voodoo_mad extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'voodoo_do', 'VOODOO_MAD_DO');
  }
}
