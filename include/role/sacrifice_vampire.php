<?php
/*
  ◆吸血公
  ○仕様
  ・身代わり：自分の感染者
  ・吸血：通常
*/
class Role_sacrifice_vampire extends Role{
  function __construct(){ parent::__construct(); }

  function GetSacrificeList(){
    global $USERS;

    $stack = array();
    foreach($USERS->rows as $user){
      if($user->IsLive(true) && $user->IsPartner('infected', $this->GetActor()->user_no)){
	$stack[] = $user->user_no;
      }
    }
    return $stack;
  }

  function Infect($user){ $user->AddRole($this->GetActor()->GetID('infected')); }
}
