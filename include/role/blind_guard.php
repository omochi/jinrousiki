<?php
/*
  ◆夜雀 (blind_guard)
  ○仕様
  ・護衛失敗：制限なし
  ・護衛処理：目隠し
  ・狩り：なし
*/
class Role_blind_guard extends Role{
  function __construct(){ parent::__construct(); }

  function GuardFailed(){ return NULL; }

  function GuardAction($user, $flag = false){ $user->AddRole('blinder'); }

  function IsHuntTarget($user){ return false; }
}
