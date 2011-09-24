<?php
/*
  ◆滝夜叉姫 (cursed_yaksa)
  ○仕様
  ・勝利条件：自分自身の生存 + 占い師系・魔法使い系の全滅
  ・人攫い無効：占い師系・魔法使い系以外
*/
RoleManager::LoadFile('yaksa');
class Role_cursed_yaksa extends Role_yaksa{
  public $reduce_rate = 3;
  function __construct(){ parent::__construct(); }

  function Ignored($user){
    return ! ($user->IsRoleGroup('mage', 'wizard') || $user->IsRole('voodoo_killer'));
  }

  function Win($victory){
    if($this->IsDead()) return false;
    foreach($this->GetUser() as $user){
      if($user->IsLive() && ! $this->Ignored($user)) return false;
    }
    return true;
  }
}
