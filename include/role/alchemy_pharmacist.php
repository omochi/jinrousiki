<?php
/*
  ◆錬金術師 (alchemy_pharmacist)
  ○仕様
  ・毒能力鑑定/毒対象変化(村人陣営以外)
*/
class Role_alchemy_pharmacist extends RoleVoteAbility{
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
      if(! $this->IsVoted($uname) && $this->IsSameUser($target_uname) &&
	 ! $this->GetActor()->detox_flag){
	$this->GetActor()->alchemy_flag = true;
      }
    }
  }

  function FilterPoisonTarget(&$list){
    global $USERS;

    $stack = array();
    foreach($list as $uname){
      if(! $USERS->ByRealUname($uname)->IsCamp('human')) $stack[] = $uname;
    }
    $list = $stack;
  }
}
