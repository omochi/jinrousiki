<?php
class Option_topping extends SelectorRoomOptionItem {
  function  __construct() { parent::__construct(RoomOption::ROLE_OPTION); }

  function GetCaption() { return '固定配役追加モード'; }

  function GetExplain() { return '固定配役に追加する役職セットです'; }

  function LoadMessages() {
    parent::LoadMessages();
    $this->label = 'モード名';
  }
}
