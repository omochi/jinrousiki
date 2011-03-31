<?php
/*
  ◆八卦見
  ○仕様
  ・魔法：魂の占い師・精神鑑定士・ひよこ鑑定士・占星術師・騎士・死神・辻斬り・光妖精
*/
class Role_soul_wizard extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole(){
    $stack = array('soul_mage', 'psycho_mage', 'sex_mage', 'stargazer_mage',
		   'poison_guard', 'doom_assassin', 'soul_assassin', 'light_fairy');
    return GetRandom($stack);
  }
}
