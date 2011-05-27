<?php
/*
  ◆神話マニア (mania)
  ○仕様
  ・コピー：通常
*/
class Role_mania extends Role{
  function __construct(){ parent::__construct(); }

  function Copy($user, $vote_data){
    $result = $user->IsRoleGroup('mania') ? 'human' : $user->main_role;
    $this->GetActor()->ReplaceRole('mania', $result);
    $this->GetActor()->AddRole('copied');
    return $result;
  }
}
