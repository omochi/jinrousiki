<?php
/*
  ◆夢共有者 (dummy_common)
  ○仕様
  ・仲間表示：身代わり君
*/
RoleManager::LoadFile('common');
class Role_dummy_common extends Role_common{
  public $display_role = 'common';
  function __construct(){ parent::__construct(); }

  function IsCommonParter($user){ return $user->IsDummyBoy(); }
}