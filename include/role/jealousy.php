<?php
/*
  ◆橋姫 (jealousy)
  ○仕様
  ・処刑得票：ショック死 (同一キューピッド恋人限定)
*/
class Role_jealousy extends Role{
  function __construct(){ parent::__construct(); }

  function SetVoteDay($uname){
    global $USERS;
    if($USERS->ByRealUname($this->GetUname())->IsRole(true, $this->role)) $this->AddStack($uname);
  }

  function VotedReaction(){
    global $USERS;

    if(! is_array($stack = $this->GetStack())) return;
    foreach($stack as $uname => $target_uname){
      if($this->IsVoted($uname)) continue;

      $cupid_list = array(); //橋姫に投票したユーザのキューピッドの ID => 恋人の ID
      foreach($this->GetVotedUname($uname) as $voted_uname){
	$user = $USERS->ByRealUname($voted_uname);
	foreach($user->GetPartner('lovers', true) as $id) $cupid_list[$id][] = $user->user_no;
      }

      //同一キューピッドの恋人が複数いたらショック死
      foreach($cupid_list as $cupid_id => $lovers_list){
	if(count($lovers_list) > 1){
	  foreach($lovers_list as $id) $USERS->SuddenDeath($id, 'SUDDEN_DEATH_JEALOUSY');
	}
      }
    }
  }
}
