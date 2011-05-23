<?php
/*
  ◆権力者 (authority)
  ○仕様
  ・投票数が +1 される
  ・反逆者と同じ人に投票すると 0 票になる
*/
class Role_authority extends RoleVoteAbility{
  public $data_type = 'both';

  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    $vote_number++;
  }
}
