<?php
class Option_dummy_boy_selector extends SelectorRoomOptionItem {
  function  __construct() {
    parent::__construct(RoomOption::GAME_OPTION);
    $this->formtype = 'group';
    $this->collect = 'CollectValue';
    $this->value = GameOptionConfig::$default_dummy_boy;
  }

  function GetCaption() { return '初日の夜は身代わり君'; }

  function GetExplain() { return '配役は<a href="info/rule.php">ルール</a>を確認して下さい'; }

  function  GetItems() {
    $items = array(''         => new Option_no_dummy_boy(),
		   'on'       => RoomOption::Get('dummy_boy'),
		   'gm_login' => RoomOption::Get('gm_login'));
    if (isset($items[$this->value])) $items[$this->value]->value = true;
    return $items;
  }
}

class Option_no_dummy_boy extends CheckRoomOptionItem {
  function  __construct() {
    parent::__construct(RoomOption::GAME_OPTION);
    $this->formtype = 'radio';
  }

  function GetCaption() { return '身代わり君なし'; }
}
