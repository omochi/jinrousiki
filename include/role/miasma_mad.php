<?php
/*
  ◆土蜘蛛 (miasma_mad)
  ○仕様
  ・処刑投票先が生存していたら熱病を付加する
*/
class Role_miasma_mad extends RoleVoteAbility{
  var $data_type = 'action';

  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $ROLES, $USERS;

    foreach($ROLES->stack->miasma_mad as $uname => $target_uname){
      if($uname == $ROLES->stack->vote_kill_uname) continue;

      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && ! $target->IsAvoid()) $target->AddDoom(1, 'febris');
    }
  }
}
