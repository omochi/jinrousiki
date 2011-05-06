<?php
/*
  ◆かぐや姫 (moon_cupid)
  ○仕様
  ・追加役職：両方に難題 + 自分に受信者
*/
class Role_moon_cupid extends Role{
  function __construct(){ parent::__construct(); }

  function AddLoversRole(&$role, $user, $flag){
    global $SELF;
    $role .= ' challenge_lovers';
    if(! $user->IsSelf()) $SELF->AddRole($user->GetID('mind_receiver'));
  }
}
