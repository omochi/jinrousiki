<?php
/*
  ◆薬師 (pharmacist)
  ○仕様
  ・毒能力鑑定/解毒
*/
class Role_pharmacist extends RoleVoteAbility{
  public $data_type = 'action';
  public $init_stack = true;

  function __construct(){ parent::__construct(); }

  function DistinguishPoison(&$list){
    global $USERS;

    foreach($this->GetStack() as $uname => $target_uname){
      if(! $this->IsVoted($uname)){
	$list[$uname] = $USERS->ByRealUname($target_uname)->DistinguishPoison();
      }
    }
  }

  function Detox(&$list){
    foreach($this->GetStack() as $uname => $target_uname){
      if(! $this->IsVoted($uname) && $this->IsSameUser($target_uname)){
	$this->GetActor()->detox_flag = true;
	$list[$uname] = 'success';
      }
    }
  }
}
