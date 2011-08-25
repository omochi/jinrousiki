<?php
/*
  ◆仙狐 (revive_fox)
  ○仕様
  ・蘇生率：100% / 誤爆有り
  ・蘇生後：能力喪失
*/
RoleManager::LoadFile('poison_cat');
class Role_revive_fox extends Role_poison_cat{
  public $revive_rate   = 100;
  public $missfire_rate =   0;

  function __construct(){ parent::__construct(); }

  function AfterRevive(){ $this->GetActor()->LostAbility(); }
}
