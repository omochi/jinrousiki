<?php
class Option_replace_human_selector extends SelectorRoomOptionItem {
  function  __construct() {
    parent::__construct(RoomOption::ROLE_OPTION);
    $this->collect = 'CollectValue';
    $this->items_source = 'replace_human_items';
  }

  function GetCaption() { return '村人置換村'; }

  function GetExplain() { return '「村人」が全員特定の役職に入れ替わります'; }

  function LoadMessages() {
    parent::LoadMessages();
    $this->label = 'モード名';
  }
}
