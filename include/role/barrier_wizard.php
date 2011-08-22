<?php
/*
  ◆結界師 (barrier_wizard)
  ○仕様
  ・護衛失敗：特殊 (別判定)
  ・護衛処理：なし
*/
RoleManager::LoadFile('wizard');
class Role_barrier_wizard extends Role_wizard{
  public $wizard_list = array('barrier_wizard' => 'SPREAD_WIZARD_DO');
  public $result_list = array('GUARD_SUCCESS');
  public $action = 'SPREAD_WIZARD_DO';

  function __construct(){ parent::__construct(); }

  function GuardFailed(){ return false; }

  function GuardAction($user, $flag = false){}
}
