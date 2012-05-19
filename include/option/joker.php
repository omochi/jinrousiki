<?php
/*
  ◆ジョーカー村 (joker)
  ○仕様
*/
class Option_joker extends CheckRoomOptionItem {
  function __construct(){ parent::__construct(RoomOption::GAME_OPTION); }

  function GetCaption() { return 'ババ抜き村'; }

  function GetExplain() { return '誰か一人に「ジョーカー」がつきます'; }

  function Cast(&$list, &$rand) { $this->CastOnce($list, $rand, '[2]'); }
}
