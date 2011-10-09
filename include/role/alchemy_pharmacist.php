<?php
/*
  ◆錬金術師 (alchemy_pharmacist)
  ○仕様
  ・毒能力鑑定/毒対象変化(村人陣営以外)
*/
RoleManager::LoadFile('pharmacist');
class Role_alchemy_pharmacist extends Role_pharmacist{
  function __construct(){ parent::__construct(); }

  function SetDetoxFlag($uname){
    $actor = $this->GetActor();
    if(! property_exists($actor, 'detox_flag')) $actor->{$this->role . '_flag'} = true;
  }

  function FilterPoisonTarget(&$list){
    global $USERS;

    $stack = array();
    foreach($list as $uname){
      if(! $USERS->ByRealUname($uname)->IsCamp('human')) $stack[] = $uname;
    }
    $list = $stack;
  }
}
