<?php
/*
  ◆決闘者 (duelist)
  ○仕様
  ・追加役職：受信者 (自分→相手)
*/
class Role_duelist extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole($user){
    $role = $this->GetActor()->GetID('rival')
    if(! $this->IsSameUser($user->uname)) $role .= ' ' . $this->GetActor()->GetID('mind_receiver');
    return $role;
  }
}
