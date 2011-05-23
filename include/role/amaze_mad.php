<?php
/*
  ◆傘化け (amaze_mad)
  ○仕様
  ・処刑投票先が処刑されたら投票結果を隠蔽する
*/
class Role_amaze_mad extends RoleVoteAbility{
  public $data_type = 'action';
  public $init_stack = true;

  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $ROOM, $ROLES, $USERS;

    $flag = false;
    $vote_user = $USERS->ByRealUname($ROLES->stack->vote_kill_uname);
    foreach($this->GetStack() as $uname => $target_uname){
      if(! $this->IsVoted($target_uname)) continue;

      $flag = true;
      $user = $USERS->ByUname($uname);
      $vote_user->AddRole("bad_status[{$user->user_no}-{$ROOM->date}]");
    }
    if($flag){
      $ROOM->SystemMessage($USERS->GetHandleName($vote_user->uname, true), 'BLIND_VOTE');
    }
  }
}
