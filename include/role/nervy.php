<?php
/*
  ◆自信家 (nervy)
  ○仕様
  ・同一陣営に投票したらショック死する
*/
class Role_nervy extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROLES;
    if($reason == '' && $ROLES->actor->GetCamp(true) == $this->GetVoteUser()->GetCamp(true)){
      $reason = 'NERVY';
    }
  }
}
