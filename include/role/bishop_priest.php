<?php
/*
  ◆司教 (bishop_priest)
  ○仕様
  ・司祭：村人陣営以外 (死者)
  ・結果表示：奇数日 (3日目以降)
*/
RoleManager::LoadFile('priest');
class Role_bishop_priest extends Role_priest{
  public $result_date = 'odd';
  public $priest_type = 'dead';

  function __construct(){ parent::__construct(); }
}
