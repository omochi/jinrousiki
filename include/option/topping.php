<?php
/*
  ◆固定配役追加モード (topping)
*/
class Option_topping extends SelectorRoomOptionItem {
  function  __construct() {
    parent::__construct();
    $this->form_list = GameOptionConfig::${$this->source};
    if (OptionManager::$change && DB::$ROOM->IsOption($this->name)) {
      $this->value = DB::$ROOM->option_role->list[$this->name][0];
    }
  }

  function GetCaption() { return '固定配役追加モード'; }

  function GetExplain() { return '固定配役に追加する役職セットです'; }

  function LoadPost() {
    RQ::Get()->ParsePostData($this->name);
    if (is_null(RQ::Get()->{$this->name})) return false;

    $post = RQ::Get()->{$this->name};
    $flag = ! empty($post) && array_key_exists($post, $this->form_list);
    if ($flag) array_push(RoomOption::${$this->group}, sprintf('%s:%s', $this->name, $post));
    RQ::Set($this->name, $flag);
  }
}
