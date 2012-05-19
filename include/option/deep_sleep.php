<?php
/*
  ◆静寂村 (deep_sleep)
  ○仕様
*/
class Option_deep_sleep extends CheckRoomOptionItem {
  function __construct() { parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption() { return '静寂村'; }

  function GetExplain() { return '全員に「爆睡者」がつきます'; }

  function Cast(&$list, &$rand) { return $this->CastAll($list); }
}
