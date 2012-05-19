<?php
/*
  ◆探偵村 (detective)
  ○仕様
*/
class Option_detective extends CheckRoomOptionItem {
  function __construct() { parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption() { return '探偵村'; }

  function GetExplain() { return '「探偵」が登場し、初日の夜に全員に公表されます'; }

  function SetRole(&$list, $count) {
    if ($list['common'] > 0) {
      $list['common']--;
      $list['detective_common']++;
    }
    elseif ($list['human'] > 0) {
      $list['human']--;
      $list['detective_common']++;
    }
  }
}
