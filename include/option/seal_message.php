<?php
class Option_seal_message extends CheckRoomOptionItem {
  function __construct() { parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption() { return '天啓封印'; }

  function GetExplain() { return '一部の個人通知メッセージが表示されなくなります'; }
}
