<?php
class Option_sub_role_limit_hard extends CheckRoomOptionItem {
  function  __construct() {
    parent::__construct(RoomOption::ROLE_OPTION);
    $this->formtype = 'radio';
  }

  function GetCaption() { return 'サブ役職制限：HARDモード'; }
}
