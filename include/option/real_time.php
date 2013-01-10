<?php
/*
  ◆リアルタイム制 (real_time)
*/
class Option_real_time extends CheckRoomOptionItem {
  public $group = RoomOption::GAME_OPTION;
  public $type  = 'realtime';

  function GetCaption() { return 'リアルタイム制'; }

  function GetExplain() { return '制限時間が実時間で消費されます'; }

  function LoadPost() {
    RQ::Get()->ParsePostOn($this->name);
    if (RQ::Get()->{$this->name}) {
      RQ::Get()->ParsePostInt(sprintf('%s_day', $this->name), sprintf('%s_night', $this->name));
    }
    return RQ::Get()->{$this->name};
  }
}
