<?php
/*
  ◆恋司祭 (priest_jealousy)
  ○仕様
  ・役職表示：司祭
  ・司祭：恋人
  ・結果表示：偶数日 (4日目以降)
*/
RoleManager::LoadFile('priest');
class Role_priest_jealousy extends Role_priest{
  public $display_role = 'priest';
  public $priest_type  = 'lovers';

  function __construct(){ parent::__construct(); }
}
