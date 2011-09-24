<?php
/*
  ◆かぐや姫 (moon_cupid)
  ○仕様
  ・追加役職：両方に難題 + 自分に受信者
*/
RoleManager::LoadFile('cupid');
class Role_moon_cupid extends Role_cupid{
  function __construct(){ parent::__construct(); }

  function GetRole($user, $flag){
    if(! $this->IsSameUser($user->uname)) $this->GetActor()->AddRole($user->GetID('mind_receiver'));
    return $this->GetActor()->GetID('lovers') . ' challenge_lovers';
  }
}
