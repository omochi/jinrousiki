<?php
/*
  ◆猫又 (poison_cat)
  ○仕様
  ・蘇生率：25% / 誤爆有り
  ・蘇生後：なし
*/
class Role_poison_cat extends Role{
  public $revive_rate   = 25;
  public $missfire_rate =  0;

  function __construct(){ parent::__construct(); }

  protected function GetEvent(){
    global $ROOM;
    return $ROOM->IsEvent('full_revive') ? 100 : ($ROOM->IsEvent('no_revive') ? 0 : NULL);
  }

  function GetRate(){ return $this->revive_rate; }

  function GetReviveRate($flag){
    $event = $this->GetEvent();
    $rate  = is_null($event) ? $this->GetRate() : $event;
    if($flag) $rate = ceil($rate * 1.3);
    return $rate > 100 ? 100 : $rate;
  }

  function GetMissfireRate($revive_rate){
    global $ROOM;

    if($this->GetEvent() || $this->missfire_rate < 0) return 0;
    $rate = $this->missfire_rate == 0 ? floor($revive_rate / 5) : $this->missfire_rate;
    if($ROOM->IsEvent('missfire_revive')) $rate *= 2;
    return $rate > $revive_rate ? $revive_rate : $rate;
  }

  function AfterRevive(){}
}
