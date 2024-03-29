<?php
/*
  ◆豊穣神 (harvest_brownie)
  ○仕様
  ・処刑得票：会心 (村人陣営) or 凍傷 (処刑)
*/
class Role_harvest_brownie extends Role {
  function SetVoteDay($uname) {
    $this->InitStack();
    if ($this->IsRealActor()) $this->AddStackName($uname);
  }

  function VoteKillReaction() {
    foreach (array_keys($this->GetStack()) as $uname) {
      $flag = $this->IsVoted($uname);
      foreach ($this->GetVotedUname($uname) as $voted_uname) {
	$user = DB::$USER->ByRealUname($voted_uname);
	if ($user->IsDead(true) || ! Lottery::Percent(30)) continue;
	if ($flag) {
	  $user->AddDoom(1, 'frostbite');
	}
	elseif ($user->IsCamp('human', true)) {
	  $user->AddRole('critical_voter');
	}
      }
    }
  }
}
