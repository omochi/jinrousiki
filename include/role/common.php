<?php
/*
  ◆共有者 (common)
  ○仕様
  ・仲間表示：共有者系
*/
class Role_common extends Role{
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    parent::OutputAbility();
    //仲間表示
    $stack = array();
    foreach($this->GetUser() as $user){
      if($this->IsSameUser($user->uname)) continue;
      if($this->IsCommonParter($user)) $stack[] = $user->handle_name;
    }
    OutputPartner($stack, 'common_partner');
  }

  //仲間判定
  function IsCommonParter($user){ return $user->IsCommon(true); }
}
