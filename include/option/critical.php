<?php
/*
  ◆急所村 (critical)
  ○仕様
*/
class Option_critical extends CheckRoomOptionItem {
  function __construct(){
		parent::__construct('critical', '急所村', '全員に「会心」「痛恨」がつきます。');
	}

  function Cast(&$list, &$rand){
    foreach(array_keys($list) as $id) $list[$id] .= ' critical_voter critical_luck';
    return array('critical_voter', 'critical_luck');
  }
}
