<?php
/*
  ◆門番 (gatekeeper_guard)
  ○仕様
  ・護衛失敗：通常
  ・護衛処理：なし
  ・狩り：なし
*/
class Role_gatekeeper_guard extends Role{
  function __construct(){ parent::__construct(); }

  function GuardFailed(){ return false; }

  function GuardAction($user, $flag = false){}

  function IsHuntTarget($user){ return false; }
}
