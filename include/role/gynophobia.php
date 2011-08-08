<?php
/*
  ◆女性恐怖症 (gynophobia)
  ○仕様
  ・ショック死：女性に投票
*/
class Role_gynophobia extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetVoteUser()->IsFemale()) $reason = 'GYNOPHOBIA';
  }
}
