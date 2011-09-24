<?php
/*
  ◆魔法使い (wizard)
  ○仕様
  ・魔法：占い師・精神鑑定士・ひよこ鑑定士・狩人・暗殺者
*/
class Role_wizard extends Role{
  public $wizard_list = array(
    'mage' => 'MAGE_DO', 'psycho_mage' => 'MAGE_DO', 'guard' => 'GUARD_DO',
    'assassin' => 'ASSASSIN_DO', 'sex_mage' => 'MAGE_DO');
  public $result_list = array('MAGE_RESULT', 'GUARD_SUCCESS', 'GUARD_HUNTED');
  public $action = 'WIZARD_DO';

  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2) foreach($this->result_list as $result) OutputSelfAbilityResult($result);
    if(isset($this->action) && $ROOM->date > 1 && $ROOM->IsNight()){
      OutputVoteMessage('wizard-do', 'wizard_do', $this->action);
    }
  }

  function GetWizardList(){ return $this->wizard_list; }

  function GetRole(){
    global $ROOM;

    $wizard_list = $this->GetWizardList();
    $stack = is_null($this->action) ? $wizard_list : array_keys($wizard_list);

    $role = $ROOM->IsEvent('full_wizard') ? array_shift($stack) :
      ($ROOM->IsEvent('debilitate_wizard') ? array_pop($stack) : GetRandom($stack));
    return is_null($this->action) ? $role : array($role, $wizard_list[$role]);
  }
}
