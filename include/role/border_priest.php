<?php
/*
  ◆境界師 (border_priest)
  ○仕様
  ・司祭：自分への投票人数 (2日目以降)
*/
RoleManager::LoadFile('priest');
class Role_border_priest extends Role_priest{
  function __construct(){ parent::__construct(); }

  protected function GetOutputRole(){
    global $ROOM;
    return $ROOM->date > 2 ? $this->role : NULL;
  }

  function Priest($role_flag, $data){
    global $ROOM, $USERS;

    if($ROOM->date < 2) return false;
    $event = $this->GetEvent();
    foreach($role_flag->{$this->role} as $uname){
      $user  = $USERS->ByUname($uname);
      $count = 0;
      foreach($ROOM->vote as $vote_stack){
	foreach($vote_stack as $stack){
	  if($user->IsSame($stack['target_uname'])) $count++;
	}
      }
      $ROOM->SystemMessage($user->handle_name . "\t" . $count, $event);
    }
  }
}
