<?php
/*
  ◆霊界で配役を公開しない (セレクタ)
*/
class Option_not_open_cast_selector extends SelectorRoomOptionItem {
  public $group = RoomOption::GAME_OPTION;
  public $formtype = 'group';
  public $item_list = array('not_open_cast', 'auto_open_cast');

  function __construct() {
    parent::__construct();
    $this->value = GameOptionConfig::$default_not_open_cast;
  }

  function GetCaption() { return '霊界で配役を公開しない'; }

  function GetItems() {
    $items = array(''               => OptionManager::GetClass('not_close_cast'),
		   'not_open_cast'  => OptionManager::GetClass('not_open_cast'),
		   'auto_open_cast' => OptionManager::GetClass('auto_open_cast'));
    if (isset($items[$this->value])) $items[$this->value]->value = true;
    return $items;
  }
}
