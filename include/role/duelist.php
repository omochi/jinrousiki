<?php
/*
  ◆決闘者 (duelist)
  ○仕様
  ・追加役職：受信者 (自分→相手)
*/
class Role_duelist extends Role{
  function __construct(){ parent::__construct(); }

  function AddRivalRole(&$role, $user, $flag){
    global $SELF;
    if(! $user->IsSelf()) $role .= ' ' . $SELF->GetID('mind_receiver');
  }
}
