<?php
/*
  ◆水妖姫 (succubus_escaper)
  ○仕様
  ・逃亡失敗：男性以外
  ・逃亡処理：なし
*/
class Role_succubus_escaper extends Role{
  function __construct(){ parent::__construct(); }

  function EscapeFailed($user){ return $user->sex != 'male'; }

  function EscapeAction($user){}
}
