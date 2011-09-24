<?php
/*
  ◆夢守人 (dummy_guard)
  ○仕様
*/
RoleManager::LoadFile('guard');
class Role_dummy_guard extends Role_guard{
  public $display_role = 'guard';
  function __construct(){ parent::__construct(); }
}
