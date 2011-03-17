<?php
/*
  ◆難題 (challenge_lovers)
  ○仕様
  ・5 日目以降恋人の相方と同じ人に投票しないとショック死する。
  ・複数の恋人がいる場合は誰か一人と同じならショック死しない。
*/
class Role_challenge_lovers extends Role{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROOM, $ROLES, $USERS;

    if($reason != '' || $ROOM->date < 5) return;

    if(! is_array($ROLES->stack->cupid_list)){ //QP のデータをセット
      $stack = array();
      foreach(array_keys($ROLES->stack->target) as $uname){
	$user = $USERS->ByRealUname($uname);
	if($user->IsLovers()){
	  foreach($user->GetPartner('lovers') as $id) $stack[$id][] = $user->user_no;
	}
      }
      //PrintData($stack, 'QP');
      $ROLES->stack->cupid_list = $stack;
    }
    $stack = array_keys($ROLES->stack->target, $ROLES->stack->target[$ROLES->actor->uname]);
    //PrintData($stack, $ROLES->actor->uname);

    foreach($ROLES->actor->GetPartner('lovers') as $cupid_id){
      foreach($ROLES->stack->cupid_list[$cupid_id] as $lovers_id){
	if($lovers_id != $ROLES->actor->user_no &&
	   in_array($USERS->ByID($lovers_id)->uname, $stack)) return;
      }
    }
    $reason = 'CHALLENGE';
  }
}
