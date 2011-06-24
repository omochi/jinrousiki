<?php
/*
  ◆女神 (mind_cupid)
  ○仕様
  ・追加役職：両方に共鳴者 + 他人撃ちなら自分に受信者
*/
class Role_mind_cupid extends Role{
  function __construct(){ parent::__construct(); }

  function AddLoversRole(&$role, $user, $flag){
    $role .= ' ' . $this->GetActor()->GetID('mind_friend');
    if(! $flag) $this->GetActor()->AddRole($user->GetID('mind_receiver'));
  }
}
