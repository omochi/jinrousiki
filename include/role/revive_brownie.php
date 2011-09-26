<?php
/*
  ◆蛇神 (revive_brownie)
  ○仕様
*/
RoleManager::LoadFile('revive_pharmacist');
class Role_revive_brownie extends Role{
  public $mix_in = 'revive_pharmacist';
  function __construct(){ parent::__construct(); }
}
