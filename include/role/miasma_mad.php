<?php
/*
  ◆土蜘蛛 (miasma_mad)
  ○仕様
  ・処刑投票先が生存していたら熱病を付加する
*/
class Role_miasma_mad extends RoleVoteAbility{
  var $data_type = 'action';
  var $init_stack = true;

  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $USERS;

    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && ! $target->IsAvoid()) $target->AddDoom(1, 'febris');
    }
  }
}
