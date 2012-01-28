<?php
/*
  ◆静寂村 (deep_sleep)
  ○仕様
*/
class Option_deep_sleep extends CheckRoomOptionItem {
  function __construct(){
		parent::__construct('deep_sleep', '静寂村', '全員に「爆睡者」がつきます');
	}

  function Cast(&$list, &$rand){ return $this->CastAll($list); }
}
