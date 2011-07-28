<?php
/*
  ◆結界師 (barrier_wizard)
  ○仕様
  ・護衛失敗：特殊 (別判定)
  ・護衛処理：なし
*/
class Role_barrier_wizard extends Role{
  function __construct(){ parent::__construct(); }

  function GuardFailed(){ return false; }

  function GuardAction($user, $flag = false){}
}
