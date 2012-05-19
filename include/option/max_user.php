<?php
class Option_max_user extends SelectorRoomOptionItem {
  function  __construct() {
    parent::__construct(RoomOption::NOT_OPTION);
    $this->collect = null;
    $this->conf_name = RoomConfig::$max_user_list;
    $this->items_source = 'max_user_list';
    $this->value = RoomConfig::$default_max_user;
  }

  function GetCaption() { return '最大人数'; }

  function GetExplain() { return '配役は<a href="info/rule.php">ルール</a>を確認して下さい'; }
}
