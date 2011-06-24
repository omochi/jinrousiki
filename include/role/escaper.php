<?php
/*
  ◆逃亡者 (escaper)
  ○仕様
  ・逃亡失敗：人狼系
  ・逃亡処理：なし
*/
class Role_escaper extends Role{
  function __construct(){ parent::__construct(); }

  function EscapeFailed($user){ return $user->IsWolf(); }

  function EscapeAction($user){}
}
