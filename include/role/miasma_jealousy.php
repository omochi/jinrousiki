<?php
/*
  ◆蛇姫 (miasma_jealousy)
  ○仕様
  ・処刑投票：熱病付加 (恋人限定・確率)
*/
class Role_miasma_jealousy extends RoleVoteAbility{
  public $data_type = 'action';
  public $init_stack = true;
  function __construct(){ parent::__construct(); }

  function VoteAction(){
    global $USERS;

    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;
      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && $target->IsLovers() && mt_rand(1, 10) > 6){
	$target->AddDoom(1, 'febris');
      }
    }
  }
}
