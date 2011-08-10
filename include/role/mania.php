<?php
/*
  ◆神話マニア (mania)
  ○仕様
  ・コピー：メイン役職
*/
class Role_mania extends Role{
  public $copied = 'copied';

  function __construct(){ parent::__construct(); }

  function Copy($user, $vote_data){
    return $this->ChangeRole($user->IsRoleGroup('mania') ? 'human' : $user->main_role);
  }

  private function ChangeRole($role){
    $this->GetActor()->ReplaceRole($this->role, $role);
    $this->GetActor()->AddRole($this->copied);
    return $role;
  }
}
