<?php
/*
  ◆吸血公
  ○仕様
  ・身代わり：自分の感染者
*/
RoleManager::LoadFile('vampire');
class Role_sacrifice_vampire extends Role_vampire{
  function __construct(){ parent::__construct(); }

  function GetSacrificeList(){
    $stack = array();
    foreach($this->GetUser() as $user){
      if($user->IsLive(true) && $user->IsPartner('infected', $this->GetActor()->user_no)){
	$stack[] = $user->user_no;
      }
    }
    return $stack;
  }
}
