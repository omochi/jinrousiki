<?php
class Option_not_open_cast_selector extends SelectorRoomOptionItem {
	function  __construct() {
		parent::__construct(RoomOption::GAME_OPTION);
		$this->formtype = 'group';
		$this->collect = 'CollectValue';
	}

	function LoadMessages() {
		$this->caption = '霊界で配役を公開しない';
	}

	function GetItems() {
		return array(
			'' => new Option_not_close_cast(),
			'on' => RoomOption::Get('not_open_cast'),
			'auto' => RoomOption::Get('auto_open_cast'),
		);
	}
}

class Option_not_close_cast extends CheckRoomOptionItem {
	function  __construct() {
		parent::__construct(RoomOption::GAME_OPTION);
		$this->formtype = 'radio';
		$this->collect = null;
	}

	function LoadMessages() {
		$this->explain = '常時公開 (蘇生能力は無効です)';
	}
}
