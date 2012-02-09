<?php
class Option_room_comment extends TextRoomOptionItem {
	function  __construct() {
		parent::__construct(RoomOption::NOT_OPTION);
		$this->collect = null;
	}

	function LoadMessages() {
		parent::LoadMessages();
		$this->caption = '村についての説明';
		$this->footer = '';
	}
}
