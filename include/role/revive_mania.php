<?php
/*
  ◆五徳猫 (revive_mania)
  ○仕様
  ・追加役職：なし
  ・人狼襲撃：コピー先蘇生
*/
class Role_revive_mania extends Role{
  function __construct(){ parent::__construct(); }

  function AddRole($role){ return $role; }

  function WolfEatCounter($user){
    global $USERS;

    if(is_null($id = $this->GetActor()->GetMainRoleTarget())) return;
    $target = $USERS->ByID($id);
    if($target->IsDead(true) && ! $target->IsReviveLimited()) $target->Revive();
  }
}
