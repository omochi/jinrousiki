<?php
/*
  ◆人形遣い (doll_master)
  ○仕様
  ・身代わり対象者：人形
*/
class Role_doll_master extends Role{
  function __construct(){ parent::__construct(); }

  function GetSacrificeList(){
    global $USERS;

    $stack = array();
    foreach($USERS->rows as $user){
      if($user->IsLive(true) && $user->IsDoll()) $stack[] = $user->user_no;
    }
    return $stack;
  }
}
