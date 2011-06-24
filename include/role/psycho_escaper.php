<?php
/*
  ◆迷い人 (psycho_escaper)
  ○仕様
  ・逃亡失敗：嘘つき
  ・逃亡処理：なし
*/
class Role_psycho_escaper extends Role{
  function __construct(){ parent::__construct(); }

  function EscapeFailed($user){ return $user->IsLiar(); }

  function EscapeAction($user){}
}
