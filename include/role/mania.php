<?php
/*
  ◆神話マニア (mania)
  ○仕様
  ・コピー：通常
*/
class Role_mania extends Role{
  public $copied = 'copied';

  function __construct(){ parent::__construct(); }

  function Copy($user, $vote_data){
    $result = $user->IsRoleGroup('mania') ? 'human' : $user->main_role;
    $this->GetActor()->ReplaceRole($this->role, $result);
    $this->GetActor()->AddRole($this->copied);
    return $result;
  }
}
