<?php
/*
  ◆侍 (reflect_guard)
  ○仕様
  ・護衛失敗：通常
  ・護衛処理：なし
  ・狩り：通常 + 鬼陣営
*/
class Role_reflect_guard extends Role{
  function __construct(){ parent::__construct(); }

  function GuardFailed(){ return false; }

  function GuardAction($user, $flag = false){}

  function IsHuntTarget($user){ return $user->IsHuntTarget() || $user->IsOgre(); }
}
