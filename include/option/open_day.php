<?php
class Option_open_day extends CheckRoomOptionItem {
  function __construct() { parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption() { return 'オープニングあり'; }

  function GetExplain() { return 'ゲームが1日目「昼」からスタートします'; }
}
