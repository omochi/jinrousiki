<?php
/*
  ◆火車 (corpse_courier_mad)
  ○仕様
  ・処刑投票：投票先が生存していたら霊能結果を隠蔽する
*/
class Role_corpse_courier_mad extends Role{
  function __construct(){ parent::__construct(); }

  function SetVoteDay($uname){
    global $USERS;
    if($USERS->ByRealUname($this->GetUname())->IsRole(true, $this->role)) $this->AddStack($uname);
  }

  function VoteAction(){
    global $USERS;

    if(! is_array($stack = $this->GetStack())) return;
    foreach($stack as $uname => $target_uname){
      if($this->IsVoted($target_uname)){
	$USERS->ByRealUname($target_uname)->stolen_flag = true;
	return;
      }
    }
  }
}
