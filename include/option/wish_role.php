<?php
/*
  ◆役割希望制 (wish_role)
  ○仕様
*/
class Option_wish_role extends CheckRoomOptionItem {
  function __construct(){ parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption(){ return '役割希望制'; }

  function GetExplain(){ return '希望の役割を指定できますが、なれるかは運です'; }
}
