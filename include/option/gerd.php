<?php
class Option_gerd extends CheckRoomOptionItem {
  function __construct() { parent::__construct(RoomOption::ROLE_OPTION); }

  function GetCaption() { return 'ゲルト君モード'; }

  function GetExplain() { return '役職が村人固定になります [村人が出現している場合のみ有効]'; }
}
