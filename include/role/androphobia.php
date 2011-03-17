<?php
/*
  ◆男性恐怖症 (androphobia)
  ○仕様
  ・男性に投票したらショック死する
*/
class Role_androphobia extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetVoteUser()->sex == 'male') $reason = 'ANDROPHOBIA';
  }
}
