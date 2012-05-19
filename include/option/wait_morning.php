<?php
class Option_wait_morning extends CheckRoomOptionItem {
  function  __construct() { parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption() { return '早朝待機制'; }

  function GetExplain() { return '夜が明けてから一定時間の間発言ができません'; }
}
