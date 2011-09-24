<?php
/*
  ◆羊飼い (shepherd_patron)
  ○仕様
  ・追加役職：羊
*/
RoleManager::LoadFile('patron');
class Role_shepherd_patron extends Role_patron{
  public $patron_role = 'mind_sheep';
  function __construct(){ parent::__construct(); }
}
