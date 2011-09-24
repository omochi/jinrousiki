<?php
/*
  ◆憑狐 (possessed_fox)
  ○仕様
*/
RoleManager::LoadFile('fox');
class Role_possessed_fox extends Role_fox{
  function __construct(){ parent::__construct(); }

  function OutputFoxAbility(){
    global $ROOM;

    if($this->GetActor()->IsActive()){
      if($ROOM->date > 1 && $ROOM->IsNight()){ //投票
	OutputVoteMessage('wolf-eat', 'possessed_do', 'POSSESSED_DO', 'POSSESSED_NOT_DO');
      }
    }
    elseif($ROOM->date > 2) OutputPossessedTarget(); //現在の憑依先
  }
}
