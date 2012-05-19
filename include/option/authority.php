<?php
/*
  ◆権力者登場 (authority)
  ○仕様
*/
class Option_authority extends CheckRoomOptionItem {
  function __construct() { parent::__construct(RoomOption::ROLE_OPTION); }

  function GetCaption() { return '権力者登場'; }

  function GetExplain() { return '投票の票数が二票になります [兼任]'; }

  function Cast(&$list, &$rand) {
    global $ROLES;
    if ($ROLES->stack->user_count >= CastConfig::${$this->name}) {
      return $this->CastOnce($list, $rand);
    }
  }
}
