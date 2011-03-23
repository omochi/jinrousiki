<?php
/*
  ◆河童 (cure_pharmacist)
  ○仕様
  ・処刑投票先を解毒/ショック死抑制できる
*/
class Role_cure_pharmacist extends RoleVoteAbility{
  var $data_type = 'action';
  var $init_stack = true;

  function __construct(){ parent::__construct(); }

  function Detox(&$list){
    foreach($this->GetStack() as $uname => $target_uname){
      if(! $this->IsVoted($uname) && $this->IsSameUser($target_uname)){
	$this->GetActor()->detox_flag = true;
	$list[$uname] = 'cured';
      }
    }
  }

  function Cure(&$list){
    foreach($this->GetStack() as $uname => $target_uname){
      if(! $this->IsVoted($uname) && $this->IsSameUser($target_uname)){
	$this->GetActor()->cured_flag = true;
	$list[$uname] = 'cured';
      }
    }
  }
}