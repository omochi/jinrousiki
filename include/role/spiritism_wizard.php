<?php
/*
  ◆交霊術師 (spiritism_wizard)
  ○仕様
  ・魔法：霊能者・雲外鏡・精神感応者・死化粧師・性別鑑定(オリジナル)
  ・魔法結果：霊能
  ・霊能：性別
*/
class Role_spiritism_wizard extends Role{
  public $action;
  public $event_list = array();

  function __construct(){ parent::__construct(); }

  function GetRole(){
    global $ROOM;

    $footer = 'necromancer';
    if($ROOM->IsEvent('full_wizard')) return 'soul_' . $footer;
    if($ROOM->IsEvent('debilitate_wizard')) return 'sex_' . $footer;
    $stack = array('', 'soul_', 'psycho_', 'embalm_', 'sex_'); //sex_necromancer は未実装
    return GetRandom($stack) . $footer;
  }

  function OutputResult(){ OutputSelfAbilityResult('SPIRITISM_WIZARD_RESULT'); }

  function Necromancer($user, $flag){ return $flag ? 'stolen' : 'sex_' . $user->sex; }
}
