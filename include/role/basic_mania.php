<?php
/*
  ◆求道者 (basic_mania)
  ○仕様
  ・コピー：基本種
*/
RoleManager::LoadFile('mania');
class Role_basic_mania extends Role_mania{
  public $copied = 'copied_basic';

  function __construct(){ parent::__construct(); }

  function Copy($user, $vote_data){
    return $this->ChangeRole($user->IsRoleGroup('mania') ? 'human' : $user->DistinguishRoleGroup());
  }
}
