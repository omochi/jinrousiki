<?php
/*
  ◆大蝙蝠
  ○仕様
  ・身代わり対象者：蝙蝠陣営
*/
class Role_boss_chiroptera extends Role{
  function __construct(){ parent::__construct(); }

  function GetSacrificeList(){
    $stack = array();
    foreach($this->GetUser() as $user){
      if(! $this->IsSameUser($user->uname) &&
	 $user->IsLiveRoleGroup('chiroptera', 'fairy')) $stack[] = $user->user_no;
    }
    return $stack;
  }
}
