<?php
/*
  ◆男性恐怖症 (androphobia)
  ○仕様
  ・ショック死：男性に投票
*/
class Role_androphobia extends RoleVoteAbility{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    if($reason == '' && $this->GetVoteUser()->IsMale()) $reason = 'ANDROPHOBIA';
  }
}
