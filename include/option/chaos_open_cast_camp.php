<?php
class Option_chaos_open_cast_camp extends CheckRoomOptionItem {
  function  __construct() {
    parent::__construct(RoomOption::ROLE_OPTION);
    $this->formtype = 'radio';
  }

  function GetName() { return '陣営通知'; }

  function GetCaption() { return '配役を通知する:陣営通知'; }

  function GetExplain() { return '陣営通知 (陣営ごとの合計を通知)'; }
}
