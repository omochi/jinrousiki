<?php
/*
  ◆蝕仙狸 (eclipse_cat)
  ○仕様
  ・蘇生率：40%
  ・誤爆率：20%
  ・蘇生後：なし
*/
class Role_eclipse_cat extends Role{
  public $missfire_rate = 20;

  function __construct(){ parent::__construct(); }

  function GetRate(){ return 40; }

  function AfterRevive(){}
}
