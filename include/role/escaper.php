<?php
/*
  ◆逃亡者
  ○仕様
  ・逃亡失敗：人狼系
*/
class Role_escaper extends Role{
  function __construct(){ parent::__construct(); }

  function EscapeFailed($user){ return $user->IsWolf(); }
}
