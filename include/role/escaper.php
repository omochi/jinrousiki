<?php
/*
  ◆逃亡者 (escaper)
  ○仕様
  ・逃亡失敗：人狼系
  ・逃亡処理：なし
*/
class Role_escaper extends Role{
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 1 && $ROOM->IsNight()){ //投票
      OutputVoteMessage('escape-do', 'escape_do', 'ESCAPE_DO');
    }
  }

  //逃亡失敗判定
  function EscapeFailed($user){ return $user->IsWolf(); }

  //逃亡処理
  function Escape($user){}
}
