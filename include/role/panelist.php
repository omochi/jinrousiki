<?php
/*
  ◆解答者 (panelist)
  ○仕様
  ・ショック死：出題者投票
  ・投票数：0
*/
RoleManager::LoadFile('chicken');
class Role_panelist extends Role_chicken {
  public $sudden_death = 'PANELIST';

  function IsSuddenDeath() {
    return ! $this->IgnoreSuddenDeath() && $this->GetVoteUser()->IsRole('quiz');
  }

  function FilterVoteDo(&$count) {
    $count = 0;
  }
}
