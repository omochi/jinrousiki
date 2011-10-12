<?php
/*
  ◆デスノート (death_note)
  ○仕様
*/
class Role_death_note extends Role{
  public $action     = 'DEATH_NOTE_DO';
  public $not_action = 'DEATH_NOTE_NOT_DO';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    if(! $this->GetActor()->IsDoomRole($this->role)) return;
    parent::OutputAbility();
    if($ROOM->date > 1 && $ROOM->IsNight()){ //投票
      OutputVoteMessage('death-note-do', 'death_note_do', $this->action, $this->not_action);
    }
  }
}
