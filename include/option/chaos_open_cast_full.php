<?php
class Option_chaos_open_cast_full extends CheckRoomOptionItem {
	function  __construct() {
		parent::__construct(RoomOption::ROLE_OPTION);
		$this->formtype = 'radio';
	}

	function LoadMessages() {
		$this->explain = '完全通知 (通常村相当)';
	}
}
