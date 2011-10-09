<?php
/*
  ◆薬師 (pharmacist)
  ○仕様
  ・毒能力鑑定/解毒
*/
class Role_pharmacist extends Role{
  public $result = 'PHARMACIST_RESULT';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 2) OutputSelfAbilityResult($this->result); //鑑定結果
  }

  function SetVoteDay($uname){
    global $USERS;
    if($USERS->ByRealUname($this->GetUname())->IsRole(true, $this->role)) $this->AddStack($uname);
  }

  //毒能力鑑定
  function DistinguishPoison(){
    global $USERS;

    if(! is_array($stack = $this->GetStack())) return;
    foreach($stack as $uname => $target_uname){
      if(! $this->IsVoted($uname)){
	$result = $USERS->ByRealUname($target_uname)->DistinguishPoison();
	$this->AddStack($result, 'pharmacist_result', $uname);
      }
    }
  }

  //解毒処理
  function Detox(){
    if(! is_array($stack = $this->GetStack())) return;
    foreach($stack as $uname => $target_uname){
      if(! $this->IsVoted($uname) && $this->IsSameUser($target_uname)){
	$this->SetDetoxFlag($target_uname);
      }
    }
  }

  //解毒フラグセット
  function SetDetoxFlag($uname){
    $this->GetActor()->detox_flag = true;
    $this->AddStack('success', 'pharmacist_result', $uname);
  }

  //ショック死抑制処理
  function Cure(){
    if(! is_array($stack = $this->GetStack())) return;
    foreach($stack as $uname => $target_uname){
      if(! $this->IsVoted($uname) && $this->IsSameUser($target_uname)){
	$this->GetActor()->cured_flag = true;
	$this->AddStack('cured', 'pharmacist_result', $uname);
      }
    }
  }

  //鑑定結果登録
  function SaveResult(){
    global $ROOM, $USERS;

    foreach($this->GetStack($this->role . '_result') as $uname => $result){
      $user = $USERS->ByUname($uname);
      $list = $this->GetStack($user->GetMainRole(true));
      $handle_name = $USERS->GetHandleName($list[$user->uname], true);
      $str = $user->handle_name . "\t" . $handle_name . "\t" . $result;
      $ROOM->SystemMessage($str, $this->result);
    }
  }
}
