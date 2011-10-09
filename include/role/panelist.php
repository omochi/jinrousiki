<?php
/*
  ◆解答者 (panelist)
  ○仕様
  ・ショック死：出題者に投票する
  ・処刑投票：投票数が 0 で固定される
*/
RoleManager::LoadFile('chicken');
class Role_panelist extends Role_chicken{
  public $sudden_death = 'PANELIST';
  function __construct(){ parent::__construct(); }

  function IsSuddenDeath(){
    return ! $this->IgnoreSuddenDeath() && $this->GetVoteUser()->IsRole('quiz');
  }

  function FilterVoteDo(&$vote_number){ $vote_number = 0; }
}
