<?php
/*
  ◆自信家 (nervy)
  ○仕様
  ・ショック死：同一陣営に投票
*/
class Role_nervy extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetActor()->GetCamp(true) == $this->GetVoteUser()->GetCamp(true)){
      $reason = 'NERVY';
    }
  }
}
