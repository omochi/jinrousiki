<?php
class Option_sub_role_limit_easy extends CheckRoomOptionItem {
	function  __construct() {
		parent::__construct(RoomOption::ROLE_OPTION);
		$this->formtype = 'radio';
	}

	function LoadMessages() {
		$this->explain = 'サブ役職制限：EASYモード';
	}
}