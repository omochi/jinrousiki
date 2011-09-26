<?php
/*
  ◆村人 (human)
  ○仕様
  ・座敷童子が生存している or 天候「疎雨」時、投票数が +1 される
*/
class Role_human extends Role{
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    if($this->IsBrownie()) $vote_number++;
  }

  private function IsBrownie(){
    global $ROOM, $ROLES;

    if(is_null($ROLES->stack->is_brownie)){
      $ROLES->stack->is_brownie = false;
      if($ROOM->IsEvent('brownie')){ //天候「疎雨」判定
	$ROLES->stack->is_brownie = true;
      }
      else{
	foreach($this->GetUser() as $user){ //座敷童子の生存判定
	  if($user->IsLiveRole('brownie')){
	    $ROLES->stack->is_brownie = true;
	    break;
	  }
	}
      }
    }
    return $ROLES->stack->is_brownie;
  }
}
