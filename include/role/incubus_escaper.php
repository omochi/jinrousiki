<?php
/*
  ◆一角獣 (incubus_escaper)
  ○仕様
  ・逃亡失敗：女性以外
  ・逃亡処理：なし
*/
class Role_incubus_escaper extends Role{
  function __construct(){ parent::__construct(); }

  function EscapeFailed($user){ return ! $user->IsFemale(); }

  function EscapeAction($user){}
}
