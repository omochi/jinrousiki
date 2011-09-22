<?php
/*
  ◆大司祭 (high_priest)
  ○仕様
  ・司祭/結果表示：司祭＆司教 (5日目以降)
*/
RoleManager::LoadFile('priest');
class Role_high_priest extends Role_priest{
  public $result_date = 'both';

  function __construct(){ parent::__construct(); }
}
