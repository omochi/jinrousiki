<?php
/*
  ◆仙人 (revive_pharmacist)
  ○仕様
  ・ショック死抑制
*/
RoleManager::LoadFile('pharmacist');
class Role_revive_pharmacist extends Role_pharmacist{
  function __construct(){ parent::__construct(); }
}
