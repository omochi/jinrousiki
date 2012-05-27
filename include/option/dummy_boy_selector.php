<?php
/*
  ◆初日の夜は身代わり君 (セレクタ)
*/
class Option_dummy_boy_selector extends SelectorRoomOptionItem {
  public $group = RoomOption::GAME_OPTION;
  public $formtype = 'group';
  public $item_list = array('dummy_boy' => 'on', 'gm_login' => 'gm_login');

  function __construct() {
    parent::__construct();
    $this->value = GameOptionConfig::$default_dummy_boy;
  }

  function GetCaption() { return '初日の夜は身代わり君'; }

  function GetExplain() { return '配役は<a href="info/rule.php">ルール</a>を確認して下さい'; }

  function GetItems() {
    $items = array(''         => new Option_no_dummy_boy(),
		   'on'       => OptionManager::GetClass('dummy_boy'),
		   'gm_login' => OptionManager::GetClass('gm_login'));
    if (isset($items[$this->value])) $items[$this->value]->value = true;
    return $items;
  }

  function LoadPost() {
    if (! isset($_POST[$this->name])) return false;
    $post = $_POST[$this->name];

    foreach ($this->item_list as $option => $value) {
      if ($post == $value) {
	RQ::$get->$option = true;
	array_push(RoomOption::${$this->group}, $option);
	break;
      }
    }
  }
}

/*
  ◆身代わり君なし
*/
class Option_no_dummy_boy extends CheckRoomOptionItem {
  public $group = RoomOption::GAME_OPTION;
  public $formtype = 'radio';

  function GetCaption() { return '身代わり君なし'; }
}
