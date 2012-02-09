<?php
class Option_room_name extends TextRoomOptionItem {
	function  __construct() {
		parent::__construct(RoomOption::NOT_OPTION);
		$this->collect = null;
	}

	function LoadMessages() {
		parent::LoadMessages();
		$this->caption = '村の名前';
		$this->footer = '村';
	}
}
