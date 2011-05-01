<?php
/*
  ◆迷い人
  ○仕様
  ・逃亡失敗：嘘つき
*/
class Role_psycho_escaper extends Role{
  function __construct(){ parent::__construct(); }

  function EscapeFailed($user){ return $user->IsLiar(); }
}
