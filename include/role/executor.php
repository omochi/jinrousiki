<?php
/*
  ◆執行者 (executor)
  ○仕様
  ・処刑投票が拮抗したら自分の投票先が非村人の場合のみ処刑される
*/
class Role_executor extends RoleVoteAbility{
  var $data_type = 'action';
  var $decide_type = 'action';

  function __construct(){ parent::__construct(); }

  function DecideVoteKill(&$uname){
    global $ROLES, $USERS;

    if(parent::DecideVoteKill($uname)) return true;
    $stack = array();
    foreach($this->GetMaxVotedUname() as $target){
      if($USERS->ByRealUname($target)->GetCamp(true) != 'human') $stack[$target] = true;
    }
    //対象を一人に固定できる時のみ有効
    if(count($stack) == 1) $uname = array_shift(array_keys($stack));
  }
}
