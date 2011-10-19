<?php
/*
  ◆恋司祭 (priest_jealousy)
  ○仕様
  ・役職表示：司祭
  ・司祭：恋人
*/
class Role_priest_jealousy extends Role{
  public $mix_in = 'priest';
  public $display_role = 'priest';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){ $this->filter->OutputAbility(); }

  function GetPriestType(){ return 'lovers'; }
}
