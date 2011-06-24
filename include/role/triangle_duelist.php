<?php
/*
  ◆舞首 (triangle_duelist)
  ○仕様
  ・追加役職：なし
*/
class Role_triangle_duelist extends Role{
  function __construct(){ parent::__construct(); }

  function AddRivalRole(&$role, $user, $flag){}
}
