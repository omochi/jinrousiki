<?php
/*
  ◆村人 (human)
  ○仕様
  ・座敷童子が生存している or 天候「疎雨」時、投票数が +1 される
*/
class Role_human extends Role{
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    global $ROOM, $USERS;

    if($ROOM->IsEvent('brownie')){ //天候「疎雨」判定
      $vote_number++;
      return;
    }

    foreach($USERS->rows as $user){ //座敷童子の生存判定
      if($user->IsLiveRole('brownie')){
	$vote_number++;
	return;
      }
    }
  }
}
