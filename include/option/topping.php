<?php
/*
  ◆固定配役追加モード (topping)
*/
class Option_topping extends SelectorRoomOptionItem {
  function  __construct() {
    parent::__construct();
    $this->item_list = GameOptionConfig::${$this->items_source};
  }

  function GetCaption() { return '固定配役追加モード'; }

  function GetExplain() { return '固定配役に追加する役職セットです'; }

  function LoadPost() {
    if (! isset($_POST[$this->name]) || empty($_POST[$this->name])) return false;
    $post = $_POST[$this->name];

    if (array_key_exists($post, $this->item_list)) {
      RQ::$get->{$this->name} = true;
      array_push(RoomOption::${$this->group}, sprintf('%s:%s', $this->name, $post));
    }
  }
}
