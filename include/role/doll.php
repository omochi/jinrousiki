<?php
/*
  ◆上海人形 (doll)
  ○仕様
  ・仲間表示：人形遣い枠
  ・勝利：人形遣い死亡
*/
class Role_doll extends Role{
  public $display_partner = true;
  public $display_doll    = false;
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    parent::OutputAbility();
    if(! $this->display_partner) return; //仲間情報表示
    $stack = array();
    if($this->display_doll) $doll_stack = array(); //人形表示判定
    foreach($this->GetUser() as $user){
      if($this->IsActor($user->uname)) continue;
      if($user->IsRole('doll_master', 'puppet_mage') || $user->IsRoleGroup('scarlet')){
	$stack[] = $user->handle_name;
      }
      if($this->display_doll && $user->IsDoll()) $doll_stack[] = $user->handle_name;
    }
    OutputPartner($stack, 'doll_master_list'); //人形遣い枠
    if($this->display_doll) OutputPartner($doll_stack, 'doll_partner'); //人形
  }

  function Win($victory){
    $this->SetStack('doll', 'class');
    foreach($this->GetUser() as $user){
      if($user->IsLiveRole('doll_master')) return false;
    }
    return true;
  }
}
