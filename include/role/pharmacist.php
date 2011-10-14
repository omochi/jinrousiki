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

  //毒能力情報セット
  function SetDetox(){
    global $USERS;

    if(! is_array($stack = $this->GetStack())) return;
    foreach($stack as $uname => $target_uname){
      if(! $this->IsVoted($uname)){
	$result = $this->DistinguishPoison($USERS->ByRealUname($target_uname));
	$this->AddStack($result, 'pharmacist_result', $uname);
      }
    }
  }

  //毒能力鑑定
  function DistinguishPoison($user){
    global $ROOM;

    //非毒能力者・夢毒者
    if(! $user->IsRoleGroup('poison') || $user->IsRole('dummy_poison')) return 'nothing';

    if($user->IsRole('strong_poison')) return 'strong'; //強毒者

    //潜毒者は 5 日目以降に強毒を持つ
    if($user->IsRole('incubate_poison')) return $ROOM->date >= 5 ? 'strong' : 'nothing';

    //騎士・誘毒者・連毒者・毒橋姫
    if($user->IsRole('poison_guard', 'guide_poison', 'chain_poison', 'poison_jealousy')){
      return 'limited';
    }
    return 'poison';
  }

  //解毒処理
  function Detox(){
    if(! is_array($stack = $this->GetStack())) return;
    foreach($stack as $uname => $target_uname){
      if(! $this->IsVoted($uname) && $this->IsActor($target_uname)){
	$this->SetDetoxFlag($uname);
      }
    }
  }

  //解毒フラグセット
  function SetDetoxFlag($uname){
    $this->GetActor()->detox = true;
    $this->AddStack('success', 'pharmacist_result', $uname);
  }

  //ショック死抑制処理
  function Cure(){
    if(! is_array($stack = $this->GetStack())) return;
    foreach($stack as $uname => $target_uname){
      if(! $this->IsVoted($uname) && $this->IsActor($target_uname)){
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
