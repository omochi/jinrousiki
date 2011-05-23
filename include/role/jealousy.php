<?php
/*
  ◆橋姫 (jealousy)
  ○仕様
  ・同一キューピッドの恋人が揃って自分に投票したらショック死させる
*/
class Role_jealousy extends RoleVoteAbility{
  public $data_type = 'array';
  public $init_stack = true;

  function __construct(){ parent::__construct(); }

  function VotedReaction(){
    global $ROLES, $USERS;

    foreach($ROLES->stack->{$this->role} as $uname){
      if($uname == $ROLES->stack->vote_kill_uname) continue;

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
