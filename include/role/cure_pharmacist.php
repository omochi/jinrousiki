<?php
/*
  ◆河童 (cure_pharmacist)
  ○仕様
  ・処刑投票先を解毒/ショック死抑制できる
*/
class Role_cure_pharmacist extends RoleVoteAbility{
  var $data_type = 'action';
  var $init_stack = true;

  function __construct(){ parent::__construct(); }

  function Detox(&$list){
    global $ROLES;

    foreach($ROLES->stack->{$this->role} as $uname => $target_uname){
      if($uname != $ROLES->stack->vote_kill_uname && $this->IsSameUser($target_uname)){
	$ROLES->actor->detox_flag = true;
	$list[$uname] = 'cured';
      }
    }
  }

  function Cure(&$list){
    global $ROLES;

    foreach($ROLES->stack->{$this->role} as $uname => $target_uname){
      if($uname != $ROLES->stack->vote_kill_uname && $this->IsSameUser($target_uname)){
	$ROLES->actor->cured_flag = true;
	$list[$uname] = 'cured';
      }
    }
  }
}
