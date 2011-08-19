<?php
/*
  ◆八卦見
  ○仕様
  ・魔法：魂の占い師・精神鑑定士・ひよこ鑑定士・占星術師・騎士・死神・辻斬り・光妖精
  ・魔法結果：占い師・護衛・狩り・暗殺
*/
class Role_soul_wizard extends Role{
  public $action = 'WIZARD_DO';

  function __construct(){ parent::__construct(); }

  function GetRole(){
    global $ROOM;

    if($ROOM->IsEvent('full_wizard')) return 'soul_mage';
    if($ROOM->IsEvent('debilitate_wizard')) return 'sex_mage';
    $stack = array('soul_mage', 'psycho_mage', 'sex_mage', 'stargazer_mage',
		   'poison_guard', 'doom_assassin', 'soul_assassin', 'light_fairy');
    return GetRandom($stack);
  }

  function OutputResult(){
    $stack = array('MAGE_RESULT', 'GUARD_SUCCESS', 'GUARD_HUNTED', 'ASSASSIN_RESULT');
    foreach($stack as $result) OutputSelfAbilityResult($result);
  }
}
