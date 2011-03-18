<?php
/*
  ◆仙人 (revive_pharmacist)
  ○仕様
  ・処刑投票先のショック死抑制ができる
*/
class Role_revive_pharmacist extends RoleVoteAbility{
  var $data_type = 'action';
  var $init_stack = true;

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
