<?php
/*
  ◆比丘尼
  ○仕様
  ・魔法：占い師・ひよこ鑑定士・占星術師 (30%) → 魂の占い師 (100%)
*/
class Role_awake_wizard extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole(){
    global $ROOM;

    $active = $this->GetActor()->IsActive();
    if($ROOM->IsEvent('full_wizard')) return $active ? 'mage' : 'soul_mage';
    if($ROOM->IsEvent('debilitate_wizard')) return $active ? 'sex_mage' : 'soul_mage';
    $stack =  $active ? array('mage', 'sex_mage', 'stargazer_mage') : array('soul_mage');
    return GetRandom($stack);
  }
}
