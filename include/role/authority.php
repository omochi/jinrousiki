<?php
/*
  ◆権力者 (authority)
  ○仕様
  ・投票数：+1
  ・処刑投票：反逆者と同じ人に投票すると -2
*/
class Role_authority extends Role {
  function FilterVoteDo(&$count) { $count++; }

  function SetVoteDay($uname) {
    $this->SetStack($this->GetUname());
    $this->SetStack($uname, $this->role . '_uname');
  }
}
