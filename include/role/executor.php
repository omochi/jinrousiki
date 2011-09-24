<?php
/*
  ◆執行者 (executor)
  ○仕様
  ・役職表示：村人
  ・決定能力：自分の投票先が非村人の場合のみ処刑される
              対象を一人に固定できる時のみ有効
*/
class Role_executor extends RoleVoteAbility{
  public $display_role = 'human';
  public $data_type    = 'action';
  public $decide_type  = 'action';

  function __construct(){ parent::__construct(); }

  function DecideVoteKill(&$uname){
    global $USERS;

    if(parent::DecideVoteKill($uname)) return true;
    $stack = array();
    foreach($this->GetMaxVotedUname() as $target){
      if(! $USERS->ByRealUname($target)->IsCamp('human', true)) $stack[$target] = true;
    }
    if(count($stack) == 1) $uname = array_shift(array_keys($stack));
  }
}
