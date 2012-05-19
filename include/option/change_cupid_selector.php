<?php
OptionManager::Load('replace_human_selector');
class Option_change_cupid_selector extends Option_replace_human_selector {
  function  __construct() {
    parent::__construct();
    $this->items_source = 'change_cupid_items';
  }

  function GetCaption() { return 'キューピッド置換村'; }

  function GetExplain() { return '「キューピッド」が全員特定の役職に入れ替わります'; }
}
