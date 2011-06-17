<?php
/*
  ◆脱落者 (dropout)
  ○仕様
  ・自身と処刑投票が最多得票者なら自分が処刑される
*/
class Role_dropout extends RoleVoteAbility{
  public $data_type = 'action';

  function __construct(){ parent::__construct(); }

  function DecideVoteKill(&$uname){
    if(parent::DecideVoteKill($uname)) return true;
    $stack = $this->GetVotePossible();
    foreach($this->GetStack() as $actor => $target){
      if(in_array($actor,  $stack) && in_array($target, $stack)) $uname = $actor;
    }
  }
}
