<?php
/*
  ◆猫又 (poison_cat)
  ○仕様
  ・蘇生率：25%
  ・誤爆率：通常
  ・蘇生後：なし
*/
class Role_poison_cat extends Role{
  public $missfire_rate = 0;

  function __construct(){ parent::__construct(); }

  function GetRate(){ return 25; }

  function AfterRevive(){}
}
