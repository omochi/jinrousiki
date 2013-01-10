<?php
/*
  ◆出現率変動モード (boost_rate)
*/
class Option_boost_rate extends SelectorRoomOptionItem {
  function  __construct() {
    parent::__construct();
    $this->form_list = GameOptionConfig::${$this->source};
    if (OptionManager::$change && DB::$ROOM->IsOption($this->name)) {
      $this->value = DB::$ROOM->option_role->list[$this->name][0];
    }
  }

  function GetCaption() { return '出現率変動モード'; }

  function GetExplain() { return '役職の出現率に補正がかかります'; }

  function LoadPost() {
    RQ::Get()->ParsePostData($this->name);
    if (is_null(RQ::Get()->{$this->name})) return false;

    $post = RQ::Get()->{$this->name};
    $flag = array_key_exists($post, $this->form_list);
    if ($flag) array_push(RoomOption::${$this->group}, sprintf('%s:%s', $this->name, $post));
    RQ::Set($this->name, $flag);
  }
}
