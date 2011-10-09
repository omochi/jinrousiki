<?php
/*
  ◆熱病 (febris)
  ○仕様
  ・ショック死：発動当日
*/
RoleManager::LoadFile('chicken');
class Role_febris extends Role_chicken{
  public $sudden_death = 'FEBRIS';
  function __construct(){ parent::__construct(); }

  function IsSuddenDeath(){ return ! $this->IgnoreSuddenDeath() && $this->IsDoom(); }
}
