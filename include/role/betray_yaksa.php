<?php
/*
  ◆夜叉丸 (betray_yaksa)
  ○仕様
  ・勝利条件：自分自身の生存 + 蝙蝠陣営の全滅 + 村人陣営の勝利
  ・人攫い無効：蝙蝠陣営以外
*/
RoleManager::LoadFile('yaksa');
class Role_betray_yaksa extends Role_yaksa{
  function __construct(){ parent::__construct(); }

  function Ignored($user){ return ! $user->IsCamp('chiroptera', true); }

  function Win($victory){
    if($victory != 'human' || $this->IsDead()) return false;
    foreach($this->GetUser() as $user){
      if($user->IsLive() && ! $this->Ignored($user)) return false;
    }
    return true;
  }
}
