<?php
/*
  ◆仙人 (revive_pharmacist)
  ○仕様
  ・ショック死抑制
*/
class Role_revive_pharmacist extends RoleVoteAbility{
  public $data_type = 'action';
  public $init_stack = true;

  function __construct(){ parent::__construct(); }

  function Cure(&$list){
    foreach($this->GetStack() as $uname => $target_uname){
      if(! $this->IsVoted($uname) && $this->IsSameUser($target_uname)){
	$this->GetActor()->cured_flag = true;
	$list[$uname] = 'cured';
      }
    }
  }
}
