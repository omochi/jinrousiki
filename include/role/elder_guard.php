<?php
/*
  ◆老兵 (elder_guard)
  ○仕様
  ・投票数：+1
  ・護衛失敗：30%
  ・護衛処理：なし
  ・狩り：なし
*/
class Role_elder_guard extends Role{
  function __construct(){ parent::__construct(); }

  function FilterVoteDo(&$vote_number){ $vote_number++; }

  function GuardFailed(){ return mt_rand(0, 9) < 3; }

  function GuardAction($user, $flag = false){}

  function IsHuntTarget($user){ return false; }
}
