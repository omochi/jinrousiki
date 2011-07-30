<?php
/*
  ◆堕天使 (cursed_angel)
  ○仕様
  ・追加役職：なし
  ・共感者判定：別陣営
  ・得票カウンター：恋人から投票されたらショック死
*/
class Role_cursed_angel extends RoleVoteAbility{
  public $data_type = 'array';
  public $init_stack = true;

  function __construct(){ parent::__construct(); }

  function AddLoversRole(&$role, $user, $flag){}

  function IsSympathy($lovers_a, $lovers_b){ return $lovers_a->GetCamp() != $lovers_b->GetCamp(); }

  function VotedReaction(){
    global $USERS;

    foreach($this->GetStack() as $uname){
      if($this->IsVoted($uname)) continue;

      foreach($this->GetVotedUname($uname) as $voted_uname){
	$user = $USERS->ByRealUname($voted_uname);
	if($user->IsLovers()){
	  $USERS->SuddenDeath($USERS->UnameToNumber($uname), 'SUDDEN_DEATH_SEALED');
	  return;
	}
      }
    }
  }
}
