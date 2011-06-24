<?php
/*
  ◆求愛者 (self_cupid)
  ○仕様
  ・追加役職：受信者 (自分→相手)
*/
class Role_self_cupid extends Role{
  function __construct(){ parent::__construct(); }

  function AddLoversRole(&$role, $user, $flag){
    if(! $this->IsSameUser($user->uname)) $role .= ' ' . $this->GetActor()->GetID('mind_receiver');
  }
}
