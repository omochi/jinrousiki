<?php
/*
  ◆探知師 (dowser_priest)
  ○仕様
  ・司祭：サブ役職
  ・結果表示：偶数日 (4日目以降)
*/
RoleManager::LoadFile('priest');
class Role_dowser_priest extends Role_priest{
  public $priest_type = 'sub_role';

  function __construct(){ parent::__construct(); }
}
