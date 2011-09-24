<?php
/*
  ◆首領
  ○仕様
  ・身代わり対象者：村人・蝙蝠
*/
RoleManager::LoadFile('common');
class Role_sacrifice_common extends Role_common{
  function __construct(){ parent::__construct(); }

  function GetSacrificeList(){
    global $USERS;

    $stack = array();
    foreach($USERS->rows as $user){
      if($user->IsLive(true) && $user->IsRole('human', 'chiroptera')) $stack[] = $user->user_no;
    }
    return $stack;
  }
}
