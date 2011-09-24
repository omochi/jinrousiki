<?php
/*
  ◆老兵 (elder_guard)
  ○仕様
  ・投票数：+1
  ・護衛失敗：30%
  ・狩り：なし
*/
RoleManager::LoadFile('guard');
class Role_elder_guard extends Role_guard{
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){ $vote_number++; }

  function GuardFailed(){ return mt_rand(0, 9) < 3; }

  function IsHuntTarget($user){ return false; }
}
