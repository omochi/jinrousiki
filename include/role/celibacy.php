<?php
/*
  ◆独身貴族 (celibacy)
  ○仕様
  ・恋人に投票されたらショック死する
*/
class Role_celibacy extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $USERS;

    if($reason != '') return;
    foreach($this->GetVotedUname() as $uname){
      if($USERS->ByRealUname($uname)->IsLovers()){
	$reason = 'CELIBACY';
	break;
      }
    }
  }
}
