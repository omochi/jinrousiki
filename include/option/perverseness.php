<?php
/*
  ◆天邪鬼村 (perverseness)
  ○仕様
*/
class Option_perverseness extends CheckRoomOptionItem {
  function __construct(){ parent::__construct(RoomOption::ROLE_OPTION); }

  function GetCaption() { return '天邪鬼村'; }

  function GetExplain() {
    return '全員に「天邪鬼」がつきます。一部のサブ役職系オプションが強制オフになります';
  }

  function Cast(&$list, &$rand) { return $this->CastAll($list); }
}
