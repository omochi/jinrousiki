<?php
class Option_dummy_boy_selector extends SelectorRoomOptionItem {
	function  __construct() {
		parent::__construct(RoomOption::GAME_OPTION);
		$this->formtype = 'group';
		$this->collect = 'CollectValue';
	}

	function LoadMessages() {
		$this->caption = '初日の夜は身代わり君';
	}

	function  GetItems() {
		return array(
			'' => new Option_no_dummy_boy(),
			'on' => RoomOption::Get('dummy_boy'),
			'gm_login' => RoomOption::Get('gm_login'),
		);
	}
}

class Option_no_dummy_boy extends CheckRoomOptionItem {
	function  __construct() {
		parent::__construct(RoomOption::GAME_OPTION);
		$this->formtype = 'radio';
	}

	function LoadMessages() {
		$this->explain = '身代わり君なし';
	}
}
