<?php
/*
  ◆比丘尼
  ○仕様
  ・魔法：占い師・ひよこ鑑定士・占星術師 (30%) → 魂の占い師 (100%)
*/
class Role_awake_wizard extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole(){
    $stack = $this->GetActor()->IsActive() ? array('mage', 'sex_mage', 'stargazer_mage') :
      array('soul_mage');
    return GetRandom($stack);
  }
}
