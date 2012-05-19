<?php
class Option_weather extends CheckRoomOptionItem {
  function  __construct() { parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption() { return '天候あり'; }

  function GetExplain() { return '「天候」と呼ばれる特殊イベントが発生します'; }
}
