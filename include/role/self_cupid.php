<?php
/*
  ◆求愛者 (self_cupid)
  ○仕様
  ・追加役職：受信者 (自分→相手)
*/
RoleManager::LoadFile('cupid');
class Role_self_cupid extends Role_cupid{
  public $self_shoot = true;
  function __construct(){ parent::__construct(); }

  function GetRole($user, $flag){
    $role = $this->GetActor()->GetID('lovers');
    if(! $this->IsActor($user->uname)) $role .= ' ' . $this->GetActor()->GetID('mind_receiver');
    return $role;
  }
}
