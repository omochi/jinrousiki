<?php
/*
  ◆天邪鬼村 (perverseness)
  ○仕様
*/
class Option_perverseness extends CheckRoomOptionItem {
  function __construct(){
		parent::__construct('perverseness', '天邪鬼村', '全員に「天邪鬼」がつきます。一部のサブ役職系オプションが強制オフになります');
	}

  function Cast(&$list, &$rand){ return $this->CastAll($list); }
}
