<?php
/*
  ◆荼枳尼天 (succubus_yaksa)
  ○仕様
  ・勝利条件：自分自身の生存 + 男性の全滅
  ・人攫い無効：男性以外
*/
RoleManager::LoadFile('yaksa');
class Role_succubus_yaksa extends Role_yaksa{
  public $reduce_rate = 2;

  function __construct(){ parent::__construct(); }

  function Ignored($user){ return ! $user->IsMale(); }

  function Win($victory){
    if($this->IsDead()) return false;
    foreach($this->GetUser() as $user){
      if(! $this->IsSameUser($user->uname) && $user->IsLive() && $user->IsMale()) return false;
    }
    return true;
  }
}
