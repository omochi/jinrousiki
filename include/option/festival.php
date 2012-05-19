<?php
class Option_festival extends CheckRoomOptionItem {
  function  __construct() { parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption() { return 'お祭り村'; }

  function GetExplain() { return '管理人がカスタムする特殊設定です'; }
}
