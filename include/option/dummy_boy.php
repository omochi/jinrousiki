<?php
/*
  ◆初日の夜は身代わり君 (dummy_boy)
  ○仕様
*/
class Option_dummy_boy extends CheckRoomOptionItem {
  public $group = RoomOption::GAME_OPTION;

  function __construct() {
    parent::__construct();
    $this->formtype = 'radio';
  }

  function GetCaption() { return '初日の夜は身代わり君'; }

  function GetExplain() { return '身代わり君あり (初日の夜、身代わり君が狼に食べられます)'; }
}
