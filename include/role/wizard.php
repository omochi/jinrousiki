<?php
/*
  ◆魔法使い
  ○仕様
  ・魔法：占い師・精神鑑定士・ひよこ鑑定士・狩人・暗殺者
  ・魔法結果：占い師・護衛・狩り
*/
class Role_wizard extends Role{
  public $action = 'WIZARD_DO';

  function __construct(){ parent::__construct(); }

  function GetRole(){
    global $ROOM;

    if($ROOM->IsEvent('full_wizard')) return 'mage';
    if($ROOM->IsEvent('debilitate_wizard')) return 'sex_mage';
    $stack = array('mage', 'psycho_mage', 'sex_mage', 'guard', 'assassin');
    return GetRandom($stack);
  }

  function OutputResult(){
    $stack = array('MAGE_RESULT', 'GUARD_SUCCESS', 'GUARD_HUNTED');
    foreach($stack as $result) OutputSelfAbilityResult($result);
  }
}
