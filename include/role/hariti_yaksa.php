<?php
/*
  ◆鬼子母神 (hariti_yaksa)
  ○仕様
  ・勝利条件：自分自身の生存 + 子狐系・キューピッド系・天使系の全滅 + 村人陣営以外の勝利
*/
RoleManager::LoadFile('yaksa');
class Role_hariti_yaksa extends Role_yaksa{
  public $reduce_rate = 2;

  function __construct(){ parent::__construct(); }

  function Ignored($user){
    return ! ($user->IsChildFox() || $user->IsRoleGroup('cupid', 'angel'));
  }

  function Win($victory){
    if($victory == 'human' || $this->IsDead()) return false;
    foreach($this->GetUser() as $user){
      if($user->IsLive() && ! $this->Ignored($user)) return false;
    }
    return true;
  }
}
