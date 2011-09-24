<?php
/*
  ◆犬神 (possessed_mad)
  ○仕様
*/
class Role_possessed_mad extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($this->GetActor()->IsActive()){
      if($ROOM->date > 1 && $ROOM->IsNight()){
	OutputVoteMessage('wolf-eat', 'possessed_do', 'POSSESSED_DO', 'POSSESSED_NOT_DO');
      }
    }
    elseif($ROOM->date > 2) OutputPossessedTarget(); //現在の憑依先
  }
}
