<?php
class Option_max_user extends SelectorRoomOptionItem {
	function  __construct() {
		parent::__construct(RoomOption::NOT_OPTION);
		$this->collect = null;
		$this->conf_name = 'ROOM_CONF';
		$this->items_source = 'max_user_list';
	}

	function LoadMessages() {
		global $ROOM_CONF;
		$this->caption = '最大人数';
		$this->explain = '配役は<a href="info/rule.php">ルール</a>を確認して下さい';
	}
}
