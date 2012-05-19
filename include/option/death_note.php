<?php
class Option_death_note extends CheckRoomOptionItem {
  function  __construct() { parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption() { return 'デスノート村'; }

  function GetExplain() { return '毎日、誰か一人に「デスノート」が与えられます'; }
}
