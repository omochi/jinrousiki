<?php
/*
  ◆道化師 (pierrot_wizard)
  ○仕様
  ・魔法：魂の占い師・ひよこ鑑定士・暗殺(特殊)・草妖精・星妖精・花妖精・氷妖精・妖精(特殊)
  ・暗殺：死の宣告 (2-10日後)
*/
RoleManager::LoadFile('wizard');
class Role_pierrot_wizard extends Role_wizard{
  public $wizard_list = array(
    'soul_mage' => 'MAGE_DO', 'pierrot_wizard' => 'ASSASSIN_DO', 'grass_fairy' => 'FAIRY_DO',
    'star_fairy' => 'FAIRY_DO', 'flower_fairy' => 'FAIRY_DO', 'ice_fairy' => 'FAIRY_DO',
    'pierrot_fairy' => 'FAIRY_DO', 'sex_mage' => 'MAGE_DO');
  public $result_list = array('MAGE_RESULT');

  function __construct(){ parent::__construct(); }

  function Assassin($user, &$list, &$reverse){
    if($user->IsLive(true)) $user->AddDoom(mt_rand(2, 10), 'death_warrant');
  }
}
