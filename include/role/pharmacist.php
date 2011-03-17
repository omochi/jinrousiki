<?php
/*
  ◆薬師 (pharmacist)
  ○仕様
  ・処刑投票先の毒の種類が分かり、解毒できる
*/
class Role_pharmacist extends RoleVoteAbility{
  var $data_type = 'action';
  var $init_stack = true;

  function __construct(){ parent::__construct(); }

  function DistinguishPoison(&$list){
    global $ROLES, $USERS;

    foreach($ROLES->stack->{$this->role} as $uname => $target_uname){
      if($uname != $ROLES->stack->vote_kill_uname){
	$list[$uname] = $USERS->ByRealUname($target_uname)->DistinguishPoison();
      }
    }
  }

  function Detox(&$list){
    global $ROLES;

    foreach($ROLES->stack->{$this->role} as $uname => $target_uname){
      if($this->IsSameUser($target_uname)){
	$ROLES->actor->detox_flag = true;
	$list[$uname] = 'success';
      }
    }
  }
}
