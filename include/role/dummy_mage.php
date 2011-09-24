<?php
/*
  ◆夢見人 (dummy_mage)
  ○仕様
  ・役職表示：占い師
*/
RoleManager::LoadFile('mage');
class Role_dummy_mage extends Role_mage{
  public $display_role = 'mage';
  function __construct(){ parent::__construct(); }
}
