<?php
/*
  ◆ジョーカー村 (joker)
  ○仕様
*/
class Option_joker extends CheckRoomOptionItem {
  function __construct(){
		parent::__construct('joker', 'ババ抜き村', '誰か一人に「ジョーカー」がつきます');
	}

  function Cast(&$list, &$rand){ $this->CastOnce($list, $rand, '[2]'); }
}
