<?php
/*
  ◆夜雀 (blind_guard)
  ○仕様
  ・狩り：なし
*/
class Role_blind_guard extends Role{
  function __construct(){ parent::__construct(); }

  function IsHuntTarget($user){ return false; }
}
