<?php
/*
  ◆鵺 (unknown_mania)
  ○仕様
  ・追加役職：なし
*/
class Role_unknown_mania extends Role{
  function __construct(){ parent::__construct(); }

  function AddManiaRole(&$role){ return true; }
}
