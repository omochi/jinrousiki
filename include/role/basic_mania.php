<?php
/*
  ◆求道者 (basic_mania)
  ○仕様
  ・コピー：基本種
*/
class Role_basic_mania extends Role{
  public $copied = 'copied_basic';

  function __construct(){ parent::__construct(); }

  function Copy($user, $vote_data){
    return $this->ChangeRole($user->IsRoleGroup('mania') ? 'human' : $user->DistinguishRoleGroup());
  }

  private function ChangeRole($role){
    $this->GetActor()->ReplaceRole($this->role, $role);
    $this->GetActor()->AddRole($this->copied);
    return $role;
  }
}
