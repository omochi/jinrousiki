<?php
/*
  ◆不審者 (suspect)
  ○仕様
  ・役職表示：村人
*/
class Role_suspect extends Role{
  public $display_role = 'human';
  function __construct(){ parent::__construct(); }
}
