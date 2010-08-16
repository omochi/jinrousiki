<?php
/*
  ◆執行者 (executor)
  ○仕様
  ・処刑投票が拮抗したら自分の投票先が非村人の場合のみ処刑される
*/
class Role_executor extends Role{
  function Role_executor(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function SetVoteAbility($uname){
    global $ROLES, $USERS;

    $user = $USERS->ByRealUname($ROLES->actor->uname);
    if($user->IsRole('executor')) $ROLES->stack->executor[] = $user->uname;
  }

  function DecideVoteKill(&$uname){
    global $ROOM, $ROLES, $USERS;

    if($uname != '' || ! is_array($ROLES->stack->executor)) return;
    $stack = array();
    foreach($ROLES->stack->executor as $actor_uname){ //最多得票者に投票した執行者の投票先を収集
      $target = $USERS->ByVirtualUname($ROOM->vote[$actor_uname]['target_uname']);
      if(in_array($target->uname, $ROLES->stack->max_voted) &&
	 $target->GetCamp(true) != 'human'){ //最多得票者リストは仮想ユーザ
	$stack[$target->uname] = true;
      }
    }
    //PrintData($stack);
    //対象を一人に固定できる時のみ有効
    if(count($stack) == 1) $uname = array_shift(array_keys($stack));
  }
}