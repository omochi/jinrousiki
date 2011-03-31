<?php
/*
  ◆毘沙門天
  ○仕様
  ・勝利条件：自分自身の生存 + 自分よりサブ役職の所持数が多い人の全滅
*/
class Role_dowser_yaksa extends Role{
  var $resist_rate = 40;

  function __construct(){ parent::__construct(); }

  function Ignored($user){ return count($user->role_list) == 1; }

  function GetReduceRate(){ return 1 / 2; }

  function DistinguishVictory($victory){
    global $ROLES, $USERS;

    if($this->IsDead()) return false;
    $count = count($ROLES->actor->role_list);
    foreach($USERS->rows as $user){
      if($user->IsLive() && count($user->role_list) > $count) return false;
    }
    return true;
  }
}
