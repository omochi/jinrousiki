<?php
class Option_gray_random extends CheckRoomOptionItem {
  function  __construct() { parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption() { return 'グレラン村'; }
}
