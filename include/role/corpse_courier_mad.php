<?php
/*
  ◆火車 (corpse_courier_mad)
  ○仕様
  ・処刑投票：投票先が生存していたら霊能結果を隠蔽する
*/
class Role_corpse_courier_mad extends RoleVoteAbility{
  public $data_type = 'action';
  public $init_stack = true;
  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $USERS;

    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($target_uname)){
	$USERS->ByRealUname($target_uname)->stolen_flag = true;
	return;
      }
    }
  }
}
