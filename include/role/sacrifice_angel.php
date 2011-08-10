<?php
/*
  ◆守護天使
  ○仕様
  ・追加役職：庇護者 (自分以外)
  ・共感者判定：常時有効
  ・人狼襲撃耐性：常時無効
*/
class Role_sacrifice_angel extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole($user, $flag){
    $role = $this->GetActor()->GetID('lovers');
    if(! $this->IsSameUser($user->uname)) $role .= ' ' . $this->GetActor()->GetID('protected');
    return $role;
  }

  function IsSympathy($lovers_a, $lovers_b){ return true; }

  function WolfEatResist(){ return true; }
}
