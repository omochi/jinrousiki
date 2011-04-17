<?php
/*
  ◆道化師
  ○仕様
  ・魔法：魂の占い師・ひよこ鑑定士・死神(ランダム)・花妖精・星妖精・草妖精・氷妖精・特殊妖精
*/
class Role_pierrot_wizard extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole(){
    $stack = array('soul_mage', 'sex_mage', 'doom_assassin', 'pierrot_fairy',
		   'flower_fairy', 'star_fairy', 'grass_fairy', 'ice_fairy');
    return GetRandom($stack);
  }
}
