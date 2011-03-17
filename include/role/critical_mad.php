<?php
/*
  ◆釣瓶落とし (critical_mad)
  ○仕様
  ・処刑投票先が生存していたら痛恨を付加する
*/
class Role_critical_mad extends RoleVoteAbility{
  var $data_type = 'action';
  var $init_stack = true;

  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $ROLES, $USERS;

    foreach($ROLES->stack->{$this->role} as $uname => $target_uname){
      if($uname == $ROLES->stack->vote_kill_uname) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && ! $target->IsAvoid()) $target->AddRole('critical_luck');
    }
  }
}
