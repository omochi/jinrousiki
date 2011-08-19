<?php
/*
  ◆釣瓶落とし (critical_mad)
  ○仕様
  ・処刑投票：投票先が生存していたら痛恨を付加する
*/
class Role_critical_mad extends RoleVoteAbility{
  public $data_type = 'action';
  public $init_stack = true;

  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $USERS;

    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && ! $target->IsAvoid()) $target->AddRole('critical_luck');
    }
  }
}
