<?php
/*
  ◆連毒者 (chain_poison)
  ○仕様
  ・役職表示：村人
*/
class Role_chain_poison extends Role{
  public $display_role = 'human';
  function __construct(){ parent::__construct(); }
}
