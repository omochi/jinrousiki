<?php
/*
  ◆吟遊詩人 (minstrel_cupid)
  ○仕様
  ・追加役職：なし
*/
class Role_minstrel_cupid extends Role{
  function __construct(){ parent::__construct(); }

  function AddLoversRole(&$role, $user, $flag){}
}
