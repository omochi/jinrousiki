<?php
/*
  ◆露西亜人形 (silver_doll)
  ○仕様
  ・仲間表示：なし
*/
RoleManager::LoadFile('doll');
class Role_silver_doll extends Role_doll{
  public $display_partner = false;
  function __construct(){ parent::__construct(); }
}
