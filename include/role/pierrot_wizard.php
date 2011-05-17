<?php
/*
  ◆道化師
  ○仕様
  ・魔法：魂の占い師・ひよこ鑑定士・死神(ランダム)・草妖精・星妖精・花妖精・氷妖精・特殊妖精
*/
class Role_pierrot_wizard extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole(){
    global $ROOM;

    if($ROOM->IsEvent('full_wizard')) return 'soul_mage';
    if($ROOM->IsEvent('debilitate_wizard')) return 'sex_mage';
    $stack = array('soul_mage', 'sex_mage', 'doom_assassin', 'grass_fairy',
		   'star_fairy', 'flower_fairy', 'ice_fairy', 'pierrot_fairy');
    return GetRandom($stack);
  }
}
