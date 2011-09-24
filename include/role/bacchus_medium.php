<?php
/*
  ◆神主 (bacchus_medium)
  ○仕様
  ・処刑投票：投票先が鬼陣営ならショック死させる
*/
RoleManager::LoadFile('medium');
class Role_bacchus_medium extends RoleVoteAbility{
  public $mix_in = 'medium';
  public $data_type = 'action';
  public $init_stack = true;
  function __construct(){ parent::__construct(); }

  function OutputAbility(){ $this->filter->OutputAbility(); }

  function VoteAction(){
    global $USERS;

    foreach($this->GetStack() as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;

      $target = $USERS->ByRealUname($target_uname);
      if($target->IsLive(true) && $target->IsOgre()){
	$USERS->SuddenDeath($target->user_no, 'SUDDEN_DEATH_DRUNK');
      }
    }
  }
}
