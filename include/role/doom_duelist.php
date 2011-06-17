<?php
/*
  ◆黒幕 (doom_uelist)
  ○仕様
  ・追加役職：死の宣告 (7日目)
*/
class Role_doom_duelist extends Role{
  function __construct(){ parent::__construct(); }

  function AddRivalRole(&$role, $user, $flag){ $role .= ' death_warrant[7]'; }
}
