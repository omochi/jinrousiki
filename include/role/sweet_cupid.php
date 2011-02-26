<?php
/*
  ◆弁財天 (sweet_cupid)
  ○仕様
  ・処刑投票先が生存していたら恋耳鳴を付加する
*/
class Role_sweet_cupid extends RoleVoteAbility{
  var $data_type = 'action';

  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $ROLES, $USERS;

    foreach($ROLES->stack->sweet_cupid as $uname => $target_uname){
      if($uname == $ROLES->stack->vote_kill_uname) continue;

      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true)) $target->AddRole('sweet_ringing');
    }
  }
}
