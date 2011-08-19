<?php
/*
  ◆猫神 (sacrifice_cat)
  ○仕様
  ・蘇生率：100%
  ・誤爆率：無し
  ・蘇生後：死亡
*/
class Role_sacrifice_cat extends Role{
  public $missfire_rate = -1;

  function __construct(){ parent::__construct(); }

  function GetRate(){ return 100; }

  function AfterRevive(){
    global $USERS;
    $USERS->Kill($this->GetActor()->user_no, 'SACRIFICE');
  }
}
