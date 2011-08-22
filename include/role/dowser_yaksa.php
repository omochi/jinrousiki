<?php
/*
  ◆毘沙門天 (dowser_yaksa)
  ○仕様
  ・勝利条件：自分自身の生存 + 自分よりサブ役職の所持数が多い人の全滅
  ・人攫い無効：サブ役職未所持
*/
RoleManager::LoadFile('yaksa');
class Role_dowser_yaksa extends Role_yaksa{
  public $resist_rate = 40;
  public $reduce_rate = 2;

  function __construct(){ parent::__construct(); }

  function Ignored($user){ return count($user->role_list) == 1; }

  function Win($victory){
    if($this->IsDead()) return false;
    $count = count($this->GetActor()->role_list);
    foreach($this->GetUser() as $user){
      if($user->IsLive() && count($user->role_list) > $count) return false;
    }
    return true;
  }
}
