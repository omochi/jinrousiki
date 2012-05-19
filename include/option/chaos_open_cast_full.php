<?php
class Option_chaos_open_cast_full extends CheckRoomOptionItem {
  function  __construct() {
    parent::__construct(RoomOption::ROLE_OPTION);
    $this->formtype = 'radio';
  }

  function GetName() { return '完全通知'; }

  function GetCaption() { return '配役を通知する:完全通知'; }

  function GetExplain() { return '完全通知 (通常村相当)'; }
}
