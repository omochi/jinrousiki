<?php
class Option_auto_open_cast extends CheckRoomOptionItem {
	function  __construct() {
		parent::__construct(RoomOption::GAME_OPTION);
		$this->formtype = 'radio';
	}

	function LoadMessages() {
		$this->explain = '自動公開 (蘇生能力者などが能力を持っている間だけ霊界が非公開になります)';
	}
}
