<?php
/*
  ◆決闘者 (duelist)
  ○仕様
  ・追加役職：受信者 (自分→相手)
*/
RoleManager::LoadFile('valkyrja_duelist');
class Role_duelist extends Role_valkyrja_duelist{
  public $self_shoot = true;
  function __construct(){ parent::__construct(); }

  function GetRole($user){
    $role = parent::GetRole($user);
    if(! $this->IsSameUser($user->uname)) $role .= ' ' . $this->GetActor()->GetID('mind_receiver');
    return $role;
  }
}
