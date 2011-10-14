<?php
/*
  ◆占い師 (mage)
  ○仕様
  ・占い：通常
*/
class Role_mage extends Role{
  public $action = 'MAGE_DO';
  public $result = 'MAGE_RESULT';
  public $mage_failed = 'failed';
  function __construct(){ parent::__construct(); }

  function OutputAbility(){
    global $ROOM;

    parent::OutputAbility();
    if($ROOM->date > 1) OutputSelfAbilityResult($this->result);
    if($ROOM->IsNight()) OutputVoteMessage('mage-do', 'mage_do', $this->action);
  }

  function IsVote(){ return true; }

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
    global $ROOM, $ROLES;

    $uname     = $this->GetUname();
    $half_moon = $ROOM->IsEvent('half_moon') && mt_rand(0, 1) > 0; //半月の判定
    $phantom   = $user->IsLiveRoleGroup('phantom') && $user->IsActive(); //幻系の判定

    if(($half_moon || $phantom)){ //厄神の護衛判定
      foreach($ROLES->LoadFilter('anti_voodoo') as $filter){
	if($filter->IsGuard($uname)) return false;
      }
    }

    //占い妨害判定
    if($half_moon || in_array($uname, $this->GetStack('jammer'))) return true;
    if($phantom){
      $this->AddSuccess($user->user_no, 'phantom');
      return true;
    }
    return false;
  }

  //呪返し判定
  function IsCursed($user){
    global $ROOM, $ROLES, $USERS;

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
	 ! $user->IsRole('white_fox', 'black_fox', 'mist_fox', 'sacrifice_fox')) ||
	$user->IsRole('spell_wisp'))){
      $USERS->Kill($user->user_no, 'FOX_DEAD');
    }
    return $this->DistinguishMage($user); //占い判定
  }

  //占い判定
  function DistinguishMage($user, $reverse = false){
    global $ROOM;

    //鬼火系判定
    if($user->IsRole('sheep_wisp') && $user->GetDoomDate('sheep_wisp') == $ROOM->date){
      return $reverse ? 'wolf' : 'human';
    }
    if($user->IsRole('wisp'))          return 'ogre';
    if($user->IsRole('foughten_wisp')) return 'chiroptera';
    if($user->IsRole('black_wisp'))    return $reverse ? 'human' : 'wolf' ;

    //特殊役職判定
    if($user->IsOgre()) return 'ogre';
    if($user->IsRoleGroup('vampire', 'mist') || $user->IsRole('boss_chiroptera')){
      return 'chiroptera';
    }

    //人狼判定
    $result = ($user->IsWolf() && ! $user->IsRole('boss_wolf') && ! $user->IsSiriusWolf()) ||
      $user->IsRole('suspect', 'cute_mage', 'black_fox', 'cute_chiroptera', 'cute_avenger');
    return ($result xor $reverse) ? 'wolf' : 'human';
  }

  //占い結果登録
  function SaveMageResult($user, $result, $action){
    global $ROOM;
    $ROOM->SystemMessage($this->GetActor()->GetHandleName($user->uname, $result), $action);
  }
}
