<?php
/*
  ◆仙狐 (revive_fox)
  ○仕様
  ・蘇生率：100%
  ・誤爆率：通常
  ・蘇生後：能力喪失
*/
class Role_revive_fox extends Role{
  public $missfire_rate = 0;

  function __construct(){ parent::__construct(); }

  function GetRate(){ return 100; }

  function AfterRevive(){ $this->GetActor()->LostAbility(); }
}
