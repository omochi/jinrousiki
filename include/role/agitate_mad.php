<?php
/*
  ◆扇動者 (agitate_mad)
  ○仕様
  ・処刑者決定：自分の投票先 + 残りをまとめてショック死
*/
class Role_agitate_mad extends Role{
  public $mix_in = 'decide';
  function __construct(){ parent::__construct(); }

  function SetVoteDay($uname){
    global $USERS;
    if($USERS->ByRealUname($this->GetUname())->IsRole(true, $this->role)) $this->AddStack($uname);
  }

  function DecideVoteKill(){
    global $USERS;

    if($this->DecideVoteKillSame()) return;
    $uname = $this->GetVoteKill();
    foreach($this->GetStack('max_voted') as $target_uname){
      if($target_uname != $uname){ //$target_uname は仮想ユーザ
	$USERS->SuddenDeath($USERS->ByRealUname($target_uname)->user_no, 'SUDDEN_DEATH_AGITATED');
      }
    }
  }
}
