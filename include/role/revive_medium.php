<?php
/*
  ◆風祝 (revive_medium)
  ○仕様
  ・蘇生率：25% / 誤爆有り
*/
RoleManager::LoadFile('medium');
RoleManager::LoadFile('poison_cat');
class Role_revive_medium extends Role_poison_cat{
  public $mix_in = 'medium';
  public $revive_rate   = 25;
  public $missfire_rate =  0;
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    $this->filter->OutputAbility();
    $this->OutputReviveAbility();
  }
}
