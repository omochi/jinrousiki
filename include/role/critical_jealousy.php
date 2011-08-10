<?php
/*
  ◆人魚 (critical_jealousy)
  ○仕様
  ・処刑投票：投票先が恋人なら自分に痛恨を付加する
*/
class Role_critical_jealousy extends RoleVoteAbility{
  public $data_type = 'action';
  public $init_stack = true;

  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $USERS;

    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $user = $USERS->ByUname($uname);
      if($user->IsDead(true)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && $target->IsLovers()) $user->AddRole('critical_luck');
    }
  }
}
