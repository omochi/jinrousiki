<?php
/*
  ◆狂信者 (fanatic_mad)
  ○仕様
*/
class Role_fanatic_mad extends Role{
  function __construct(){ parent::__construct(); }

  protected function OutputPartner(){
    $stack = array();
    foreach($this->GetUser() as $user){
      if($user->IsRole('possessed_wolf')){
	$stack[] = DB::$USER->GetHandleName($user->uname, true); //憑依先を追跡する
      }
      elseif($user->IsWolf(true)){
	$stack[] = $user->handle_name;
      }
    }
    OutputPartner($stack, 'wolf_partner');
  }
}
