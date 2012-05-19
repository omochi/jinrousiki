<?php
class Option_chaos extends CheckRoomOptionItem {
  function  __construct() { parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption() { return '闇鍋モード'; }
}
