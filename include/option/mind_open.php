<?php
/*
  ◆白夜村 (mind_open)
  ○仕様
*/
class Option_mind_open extends CheckRoomOptionItem {
  function __construct(){ parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption() { return '白夜村'; }

  function GetExplain() { return '全員に「公開者」がつきます'; }

  function Cast(&$list, &$rand) { return $this->CastAll($list); }
}
