<?php
/*
  ◆薬師 (pharmacist)
  ○仕様
  ・毒能力鑑定/解毒
*/
class Role_pharmacist extends RoleVoteAbility{
  public $data_type = 'action';
  public $init_stack = true;
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2) OutputSelfAbilityResult('PHARMACIST_RESULT'); //鑑定結果
  }

  //毒能力鑑定
  function DistinguishPoison(&$list){
    global $USERS;

    foreach($this->GetStack() as $uname => $target_uname){
      if(! $this->IsVoted($uname)){
	$list[$uname] = $USERS->ByRealUname($target_uname)->DistinguishPoison();
      }
    }
  }

  //解毒処理
  function Detox(&$list){
    foreach($this->GetStack() as $uname => $target_uname){
      if(! $this->IsVoted($uname) && $this->IsSameUser($target_uname)){
	$this->SetDetoxFlag($list, $uname);
      }
    }
  }

  //解毒フラグセット
  function SetDetoxFlag(&$list, $uname){
    $this->GetActor()->detox_flag = true;
    $list[$uname] = 'success';
  }

  //ショック死抑制処理
  function Cure(&$list){
    foreach($this->GetStack() as $uname => $target_uname){
      if(! $this->IsVoted($uname) && $this->IsSameUser($target_uname)){
	$this->GetActor()->cured_flag = true;
	$list[$uname] = 'cured';
      }
    }
  }
}
