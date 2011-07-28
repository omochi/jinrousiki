<?php
/*
  ◆騎士 (poison_guard)
  ○仕様
  ・護衛失敗：制限なし
  ・護衛処理：なし
  ・狩り：通常
*/
class Role_poison_guard extends Role{
  function __construct(){ parent::__construct(); }

  function GuardFailed(){ return NULL; }

  function GuardAction($user, $flag = false){}

  function IsHuntTarget($user){ return $user->IsHuntTarget(); }
}
