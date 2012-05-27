<?php
/*
  ◆出現率変動モード (boost_rate)
*/
class Option_boost_rate extends SelectorRoomOptionItem {
  function  __construct() {
    parent::__construct();
    $this->item_list = GameOptionConfig::${$this->items_source};
  }

  function GetCaption() { return '出現率変動モード'; }

  function GetExplain() { return '役職の出現率に補正がかかります'; }

  function LoadPost() {
    if (! isset($_POST[$this->name]) || empty($_POST[$this->name])) return false;
    $post = $_POST[$this->name];

    if (array_key_exists($post, $this->item_list)) {
      RQ::$get->{$this->name} = true;
      array_push(RoomOption::${$this->group}, sprintf('%s:%s', $this->name, $post));
    }
  }
}
