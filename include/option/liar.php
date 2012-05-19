<?php
/*
  ◆狼少年村 (liar)
  ○仕様
*/
class Option_liar extends CheckRoomOptionItem {
  function __construct() { parent::__construct(RoomOption::ROLE_OPTION); }

  function GetCaption() { return '狼少年村'; }

  function GetExplain() { return 'ランダムで「狼少年」がつきます'; }

  function Cast(&$list, &$rand) {
    foreach (array_keys($list) as $id) {
      if (mt_rand(0, 9) < 6) $list[$id] .= ' ' . $this->name;
    }
    return array($this->name);
  }
}
