<?php
/*
  ◆決闘村 (duel)
  ○仕様
*/
class Option_duel extends CheckRoomOptionItem {
  function  __construct() { parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption() { return '決闘村'; }
}
