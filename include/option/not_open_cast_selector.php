<?php
class Option_not_open_cast_selector extends SelectorRoomOptionItem {
  function  __construct() {
    parent::__construct(RoomOption::GAME_OPTION);
    $this->formtype = 'group';
    $this->collect = 'CollectValue';
    $this->value = GameOptionConfig::$default_not_open_cast;
  }

  function GetCaption() { return '霊界で配役を公開しない'; }

  function GetItems() {
    $items = array(''               => new Option_not_close_cast(),
		   'not_open_cast'  => RoomOption::Get('not_open_cast'),
		   'auto_open_cast' => RoomOption::Get('auto_open_cast'));
    if (isset($items[$this->value])) $items[$this->value]->value = true;
    return $items;
  }
}

class Option_not_close_cast extends CheckRoomOptionItem {
  function  __construct() {
    parent::__construct(RoomOption::GAME_OPTION);
    $this->formtype = 'radio';
    $this->collect = null;
  }

  function GetCaption() { return '常時公開 (蘇生能力は無効です)'; }
}
