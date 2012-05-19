<?php
class Option_gm_login extends CheckRoomOptionItem {
  function __construct() {
    parent::__construct(RoomOption::GAME_OPTION);
    $this->formtype = 'radio';
  }

  function GetCaption() { return '身代わり君は GM'; }

  function GetExplain() { return '仮想 GM が身代わり君としてログインします'; }
}
