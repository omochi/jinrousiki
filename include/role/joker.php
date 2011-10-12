<?php
/*
  ◆ジョーカー (joker)
  ○仕様
  ・勝利判定：非最終所持 or 単独生存
*/
class Role_joker extends Role{
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;
    if($this->GetActor()->IsJoker($ROOM->date)) parent::OutputAbility();
  }

  function FilterWin(&$flag){
    global $ROOM, $USERS;
    $flag = ! $this->GetActor()->IsJoker($ROOM->date) ||
      ($this->IsLive() && count($USERS->GetLivingUsers()) == 1);
  }
}
