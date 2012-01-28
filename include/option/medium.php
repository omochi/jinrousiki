<?php
/*
  ◆巫女登場 (medium)
  ○仕様
  ・配役：村人2 → 巫女1・女神1
*/
class Option_medium extends CheckRoomOptionItem {
  function __construct(){
		parent::__construct('medium', '巫女登場', '突然死した人の所属陣営が分かります [村人2→巫女1・女神1]');
	}

  function SetRole(&$list, $count){
    global $CAST_CONF;
    if($count >= $CAST_CONF->{$this->name} && $list['human'] > 1){
      $list['human'] -= 2;
      $list[$this->name]++;
      $list['mind_cupid']++;
    }
  }
}
