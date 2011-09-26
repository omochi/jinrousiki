<?php
/*
  ◆狂骨 (critical_avenger)
  ○仕様
  ・処刑投票：投票先が生存していたら痛恨を付加する (釣瓶落とし相当)
*/
RoleManager::LoadFile('avenger');
class Role_critical_avenger extends RoleVoteAbility{
  public $mix_in = 'avenger';
  public $data_type = 'action';
  public $init_stack = true;
  function __construct(){ parent::__construct(); }

  function OutputAbility(){ $this->filter->OutputAbility(); }

  function VoteAction(){
    global $USERS;

    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && ! $target->IsAvoid()) $target->AddRole('critical_luck');
    }
  }
}
