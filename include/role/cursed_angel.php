<?php
/*
  ◆堕天使 (cursed_angel)
  ○仕様
  ・追加役職：なし
  ・共感者判定：別陣営
  ・ショック死：恋人からの得票
*/
class Role_cursed_angel extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function AddLoversRole(&$role, $user, $flag){}

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
