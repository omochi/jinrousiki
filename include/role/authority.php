<?php
/*
  ◆権力者 (authority)
  ○仕様
  ・投票数が +1 される
  ・反逆者と同じ人に投票すると０票になる
*/
class Role_authority extends Role{
  function Role_authority(){ $this->__construct(); }
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    $vote_number++;
  }

  function SetVoteAbility($uname){
    global $ROLES;
    $ROLES->stack->authority = $ROLES->actor->uname;
    $ROLES->stack->authority_uname = $uname;
  }
}
