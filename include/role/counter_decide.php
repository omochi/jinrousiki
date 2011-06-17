<?php
/*
  ◆燕返し (counter_decide)
  ○仕様
  ・自身と処刑投票が最多得票者なら自分の投票先が処刑される
*/
class Role_counter_decide extends RoleVoteAbility{
  public $data_type = 'action';

  function __construct(){ parent::__construct(); }

  function DecideVoteKill(&$uname){
    if(parent::DecideVoteKill($uname)) return true;
    $stack = $this->GetVotePossible();
    foreach($this->GetStack() as $actor => $target){
      if(in_array($actor,  $stack) && in_array($target, $stack)) $uname = $target;
    }
  }
}
