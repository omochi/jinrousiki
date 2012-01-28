<?php
/*
  ◆宵闇村 (blinder)
  ○仕様
*/
class Option_blinder extends CheckRoomOptionItem {
  function __construct(){
		parent::__construct('blinder', '宵闇村', '全員に「目隠し」がつきます');
	}

  function Cast(&$list, &$rand){ return $this->CastAll($list); }
}
