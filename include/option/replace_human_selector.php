<?php
/*
  ◆村人置換村 (セレクタ)
*/
class Option_replace_human_selector extends SelectorRoomOptionItem {
  public $on_change = ' onChange="change_replace_human()"';

  function  __construct() {
    parent::__construct();
    $this->form_list = GameOptionConfig::${$this->source};
  }

  function GetCaption() { return '村人置換村'; }

  function GetExplain() { return '「村人」が全員特定の役職に入れ替わります'; }
}
