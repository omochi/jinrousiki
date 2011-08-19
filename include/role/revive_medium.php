<?php
/*
  ◆風祝 (revive_medium)
  ○仕様
  ・蘇生率：25%
  ・誤爆率：通常
  ・蘇生後：なし
*/
class Role_revive_medium extends Role{
  public $missfire_rate = 0;

  function __construct(){ parent::__construct(); }

  function GetRate(){ return 25; }

  function AfterRevive(){}
}
