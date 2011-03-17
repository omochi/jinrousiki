<?php
/*
  ◆仙人 (revive_pharmacist)
  ○仕様
  ・処刑投票先のショック死抑制ができる
*/
class Role_revive_pharmacist extends RoleVoteAbility{
  var $data_type = 'action';
  var $init_stack = true;

  function __construct(){ parent::__construct(); }

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
