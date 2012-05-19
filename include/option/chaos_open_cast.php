<?php
class Option_chaos_open_cast extends SelectorRoomOptionItem {
  function  __construct() {
    parent::__construct(RoomOption::ROLE_OPTION);
    $this->formtype = 'group';
    $this->collect = 'CollectValue';
  }

  function  GetItems() {
    $items = array(''     => new Option_chaos_open_cast_none(),
		   'camp' => RoomOption::Get('chaos_open_cast_camp'),
		   'role' => RoomOption::Get('chaos_open_cast_role'),
		   'full' => RoomOption::Get('chaos_open_cast_full'));
    if (isset($items[$this->value])) $items[$this->value]->value = true;
    return $items;
  }

  function GetCaption() { return '配役を通知する'; }
}

class Option_chaos_open_cast_none extends CheckRoomOptionItem {
  function  __construct() {
    parent::__construct(RoomOption::ROLE_OPTION);
    $this->formtype = 'radio';
    $this->collect = null;
  }

  function GetCaption() { return '通知なし'; }
}
