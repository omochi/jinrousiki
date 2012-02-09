<?php
class Option_full_chiroptera extends CheckRoomOptionItem {
	function  __construct() {
		parent::__construct(RoomOption::ROLE_OPTION);
	}

	function LoadMessages() {
		$this->caption = '闇鍋モード';
		$this->explain = '闇鍋モード';
	}
}
