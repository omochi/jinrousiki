<?php
/*
  ◆尸解仙 (revive_mad)
  ○仕様
*/
RoleManager::LoadFile('mad');
class Role_revive_mad extends Role_mad{
  public $mix_in = 'revive_pharmacist';
  function __construct(){ parent::__construct(); }
}
