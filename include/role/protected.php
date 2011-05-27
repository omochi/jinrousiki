<?php
/*
  ◆庇護者 (protected)
  ○仕様
  ・人狼襲撃耐性：身代わり (庇護者付加者)
*/
class Role_protected extends Role{
  function __construct(){ parent::__construct(); }

  function WolfEatResist(){
    global $ROOM, $USERS;

    if($ROOM->IsEvent('no_sacrifice')) return false; //蛍火ならスキップ
    $stack = array();
    foreach($this->GetActor()->GetPartner('protected') as $id){ //生存中の身代わり能力者を検出
      if($USERS->ByID($id)->IsLive(true)) $stack[] = $id;
    }
    if(count($stack) < 1) return false;
    $USERS->Kill(GetRandom($stack), 'SACRIFICE');
    return true;
  }
}
