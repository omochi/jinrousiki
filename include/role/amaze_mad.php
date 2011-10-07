<?php
/*
  ◆傘化け (amaze_mad)
  ○仕様
  ・処刑投票：投票先が生存していたら投票結果を隠蔽する
  ・悪戯：投票結果隠蔽
*/
RoleManager::LoadFile('corpse_courier_mad');
class Role_amaze_mad extends Role_corpse_courier_mad{
  public $bad_status = 'blind_vote';
  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $ROOM, $USERS;

    if(! is_array($stack = $this->GetStack())) return;
    $flag   = false;
    $target = $USERS->ByRealUname($this->GetStack('vote_kill_uname'));
    foreach($stack as $uname => $target_uname){
      if(! $this->IsVoted($target_uname)) continue;
      $flag = true;
      $id   = $USERS->ByUname($uname)->user_no;
      $target->AddRole("bad_status[{$id}-{$ROOM->date}]");
    }
    if($flag) $ROOM->SystemMessage($USERS->GetHandleName($target->uname, true), 'BLIND_VOTE');
  }

  function SetEvent($user){
    global $ROOM;
    $ROOM->event->{$this->bad_status} = true;
  }
}
