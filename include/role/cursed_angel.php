<?php
/*
  ◆堕天使 (cursed_angel)
  ○仕様
  ・共感者判定：別陣営
  ・ショック死：恋人からの得票
*/
RoleManager::LoadFile('angel');
class Role_cursed_angel extends RoleVoteAbility{
  public $mix_in = 'angel';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){ $this->filter->OutputAbility(); }

  function IsSympathy($lovers_a, $lovers_b){ return $lovers_a->GetCamp() != $lovers_b->GetCamp(); }

  function FilterSuddenDeath(&$reason){
    global $USERS;

    if($reason != '') return;
    foreach($this->GetVotedUname() as $uname){
      if($USERS->ByRealUname($uname)->IsLovers()){
	$reason = 'SEALED';
	break;
      }
    }
  }
}
