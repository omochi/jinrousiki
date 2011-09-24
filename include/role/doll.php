<?php
/*
  ◆上海人形 (doll)
  ○仕様
*/
class Role_doll extends Role{
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $USERS;

    parent::OutputAbility();
    $stack = array();
    $flag  = $this->doll_partner; //人形表示判定
    foreach($USERS->rows as $user){
      if($this->IsSameUser($user->uname)) continue;
      if($user->IsRole('doll_master', 'puppet_mage') || $user->IsRoleGroup('scarlet')){
	$stack['master'][] = $user->handle_name;
      }
      if($flag && $user->IsDoll()) $stack['doll'][] = $user->handle_name;
    }
    OutputPartner($stack['master'], 'doll_master_list'); //人形遣い
    if($flag) OutputPartner($stack['doll'], 'doll_partner'); //人形
  }
}
