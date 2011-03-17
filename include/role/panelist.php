<?php
/*
  ◆解答者 (panelist)
  ○仕様
  ・出題者に投票するとショック死する
  ・投票数が 0 で固定される
*/
class Role_panelist extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){
    $vote_number = 0;
  }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetVoteUser()->IsRole('quiz')) $reason = 'PANELIST';
  }
}
