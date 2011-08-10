<?php
/*
  ◆山わろ (poison_avenger)
  ○仕様
  ・追加役職：なし
  ・毒：人狼系 + 妖狐陣営 + 自分の仇敵
*/
class Role_poison_avenger extends Role{
  function __construct(){ parent::__construct(); }

  function GetRole($user){ return $this->GetActor()->GetID('enemy'); }

  function FilterPoisonTarget(&$list){
    global $USERS;

    $id = $this->GetActor()->user_no;
    $stack = array();
    foreach($list as $uname){
      $user = $USERS->ByRealUname($uname);
      if($user->IsRoleGroup('wolf', 'fox') || $user->IsPartner('enemy', $id)) $stack[] = $uname;
    }
    $list = $stack;
  }
}
