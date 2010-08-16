<?php
/*
  ◆反逆者 (rebel)
  ○仕様
  ・権力者と同じ人に投票すると０票になる
*/
class Role_rebel extends Role{
  function Role_rebel(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function SetVoteAbility($uname){
    global $ROLES;
    $ROLES->stack->rebel = $ROLES->actor->uname;
    $ROLES->stack->rebel_uname = $uname;
  }

  function FilterRebel(&$message_list, &$count_list){
    global $ROLES;

    //能力発動判定
    if(is_null($ROLES->stack->authority) || is_null($ROLES->stack->rebel) ||
       $ROLES->stack->authority_uname != $ROLES->stack->rebel_uname) return;

    //権力者と反逆者の投票数を 0 にする
    $message_list[$ROLES->stack->authority]['vote_number'] = 0;
    $message_list[$ROLES->stack->rebel]['vote_number'] = 0;

    //投票先の得票数を補正する
    $uname = $ROLES->stack->rebel_uname;
    if($message_list[$uname]['voted_number'] > 3)
      $message_list[$uname]['voted_number'] -= 3;
    else
      $message_list[$uname]['voted_number'] = 0;
    $count_list[$uname] = $message_list[$uname]['voted_number'];
  }
}
