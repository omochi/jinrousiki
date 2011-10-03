<?php
/*
  ◆鏡妖精 (mirror_fairy)
  ○仕様
  ・特殊イベント (昼)：決選投票
*/
RoleManager::LoadFile('fairy');
class Role_mirror_fairy extends Role_fairy{
  public $action = 'CUPID_DO';
  public $event_day = 'vote_duel';
  function __construct(){ parent::__construct(); }

  function IsVote(){
    global $ROOM;
    return $ROOM->date ==1 && $ROOM->IsNight();
  }

  function SetEvent($type){
    global $ROOM, $USERS;

    $stack = array(); //決選投票対象者の ID リスト
    foreach($this->GetActor()->GetPartner($this->role, true) as $key => $value){ //生存確認
      if($USERS->IsVirtualLive($key))   $stack[] = $key;
      if($USERS->IsVirtualLive($value)) $stack[] = $value;
    }
    if(count($stack) > 1) $ROOM->event->{$this->{'event_' . $type}} = $stack;
  }
}
