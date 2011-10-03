<?php
/*
  ◆占い師 (mage)
  ○仕様
  ・占い：通常
*/
class Role_mage extends Role{
  public $result = 'MAGE_RESULT';
  public $mage_failed = 'failed';
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 1) OutputSelfAbilityResult($this->result);
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'mage_do', 'MAGE_DO');
  }

  //占い
  function Mage($user){
    if($this->IsJammer($user)){
      return $this->SaveMageResult($user, $this->mage_failed, $this->result);
    }
    if($this->IsCursed($user)) return false;
    $this->SaveMageResult($user, $this->GetMageResult($user), $this->result);
  }

  //占い失敗判定
  function IsJammer($user){
    global $ROOM;

    $actor = $this->GetActor();
    $half_moon = $ROOM->IsEvent('half_moon') && mt_rand(0, 1) > 0; //半月の判定
    $phantom   = $user->IsAbilityPhantom(); //幻系の判定

    //厄神の護衛判定
    if(($half_moon || $phantom) && in_array($actor->uname, $this->GetStack('anti_voodoo'))){
      $this->AddSuccess($actor->uname, 'anti_voodoo_success');
      return false;
    }

    //占い妨害判定
    if($half_moon || in_array($actor->uname, $this->GetStack('jammer'))) return true;
    if($phantom){
      $this->AddSuccess($user->user_no, 'phantom');
      return true;
    }
    return false;
  }

  //呪返し判定
  function IsCursed($user){
    global $ROOM, $USERS, $ROLES;

    if((! $ROOM->IsEvent('no_cursed') && $user->IsLiveRoleGroup('cursed')) ||
       in_array($user->uname, $this->GetStack('voodoo'))){
      foreach($ROLES->LoadFilter('anti_voodoo') as $filter){ //厄神の護衛判定
	if($filter->GuardCurse($this->GetActor())) return false;
      }
    }
    return false;
  }

  //占い結果取得
  function GetMageResult($user){
    global $ROOM, $USERS;

    //憑依キャンセル判定
    if(array_key_exists($user->uname, $this->GetStack('possessed'))) $user->possessed_cancel = true;

    //呪殺判定
    if($user->IsLive(true) && ! $ROOM->IsEvent('no_fox_dead') &&
       (($user->IsFox() && ! $user->IsChildFox() &&
	 ! $user->IsRole('white_fox', 'black_fox', 'mist_fox')) ||
	$user->IsRole('spell_wisp'))){
      $USERS->Kill($user->user_no, 'FOX_DEAD');
    }
    return $user->DistinguishMage(); //占い判定
  }

  //占い結果登録
  function SaveMageResult($user, $result, $action){
    global $ROOM;
    $ROOM->SystemMessage($this->GetActor()->GetHandleName($user->uname, $result), $action);
  }
}
