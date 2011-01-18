<?php
/*
  ◆女性恐怖症 (gynophobia)
  ○仕様
  ・女性に投票したらショック死する
*/
class Role_gynophobia extends Role{
  function __construct(){ parent::__construct(); }

  function FilterSuddenDeath(&$reason){
    global $ROLES, $USERS;
    if($reason == '' &&
       $USERS->ByRealUname($ROLES->stack->target[$ROLES->actor->uname])->sex == 'female'){
      $reason = 'GYNOPHOBIA';
    }
  }
}
