<?php
/*
  ◆人馬 (centaurus_pharmacist)
  ○仕様
  ・処刑投票先が毒を持っていたら死亡する
*/
class Role_centaurus_pharmacist extends RoleVoteAbility{
  public $data_type = 'action';
  public $init_stack = true;

  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $USERS;

    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      if($USERS->ByRealUname($target_uname)->DistinguishPoison() != 'nothing'){
	$USERS->Kill($USERS->UnameToNumber($uname), 'POISON_DEAD_day');
      }
    }
  }
}
