<?php
/*
  ◆結界師 (barrier_wizard)
  ○仕様
  ・魔法結果：護衛
  ・護衛失敗：特殊 (別判定)
  ・護衛処理：なし
*/
class Role_barrier_wizard extends Role{
  public $action = 'SPREAD_WIZARD_DO';

  function __construct(){ parent::__construct(); }

  function OutputResult(){ OutputSelfAbilityResult('GUARD_SUCCESS'); }

  function GuardFailed(){ return false; }

  function GuardAction($user, $flag = false){}
}
