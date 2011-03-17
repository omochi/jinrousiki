<?php
/*
  ◆女性恐怖症 (gynophobia)
  ○仕様
  ・女性に投票したらショック死する
*/
class Role_gynophobia extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetVoteUser()->sex == 'female') $reason = 'GYNOPHOBIA';
  }
}
