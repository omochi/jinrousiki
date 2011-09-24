<?php
/*
  ◆憑狼 (possessed_wolf)
  ○仕様
*/
RoleManager::LoadFile('wolf');
class Role_possessed_wolf extends Role_wolf{
  function __construct(){ parent::__construct(); }

  function OutputWolfAbility(){
    global $ROOM;
    if($ROOM->date > 1) OutputPossessedTarget(); //現在の憑依先
  }
}
