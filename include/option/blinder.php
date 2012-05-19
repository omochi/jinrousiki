<?php
/*
  ◆宵闇村 (blinder)
  ○仕様
*/
class Option_blinder extends CheckRoomOptionItem {
  function __construct() { parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption() { return '宵闇村'; }

  function GetExplain() { return '全員に「目隠し」がつきます'; }

  function Cast(&$list, &$rand) { return $this->CastAll($list); }
}
