<?php
/*
  ◆復讐者 (avenger)
  ○仕様
  ・追加役職：なし
*/
RoleManager::LoadFile('valkyrja_duelist');
class Role_avenger extends Role_valkyrja_duelist{
  public $partner_role   = 'enemy';
  public $partner_header = 'avenger_target';
  function __construct(){ parent::__construct(); }
}
