<?php
/*
  ◆人狼 (wolf)
  ○仕様
*/
class Role_wolf extends Role{
  function __construct(){ parent::__construct(); }

  //役職情報表示
  function OutputAbility(){
    global $ROOM, $USERS;

    parent::OutputAbility();
    //仲間情報を収集
    $stack = array();
    foreach($this->GetUser() as $user){
      if($this->IsSameUser($user->uname)) continue;
      if($user->IsRole('possessed_wolf')){
	$stack['wolf'][] = $USERS->GetHandleName($user->uname, true); //憑依先を追跡する
      }
      elseif($user->IsWolf(true)){
	$stack['wolf'][] = $user->handle_name;
      }
      elseif($user->IsRole('whisper_mad')){
	$stack['mad'][] = $user->handle_name;
      }
      elseif($user->IsRole('unconscious') || $user->IsRoleGroup('scarlet')){
	$stack['unconscious'][] = $user->handle_name;
      }
    }
    if($this->GetActor()->IsWolf(true)){
      OutputPartner($stack['wolf'], 'wolf_partner'); //人狼
      OutputPartner($stack['mad'], 'mad_partner'); //囁き狂人
    }
    if($ROOM->IsNight()) OutputPartner($stack['unconscious'], 'unconscious_list'); //無意識
    $this->OutputWolfAbility();
    if($ROOM->IsNight()) OutputVoteMessage('wolf-eat', 'wolf_eat', 'WOLF_EAT'); //投票
  }

  //特殊狼の情報表示
  function OutputWolfAbility(){}

  //人狼襲撃失敗判定
  function WolfEatSkip($user){
    global $ROOM, $ROLES;

    if($user->IsWolf()){ //人狼系判定 (例：銀狼出現)
      $this->WolfEatSkipAction($user);
      $user->wolf_killed = true; //尾行判定は成功扱い
      return true;
    }
    if($user->IsResistFox()){ //妖狐判定
      $this->FoxEatAction($user); //妖狐襲撃処理
      $ROLES->actor = $user; //妖狐襲撃カウンター処理
      $ROLES->Load('main_role', true)->FoxEatCounter($this->GetVoter());

      //人狼襲撃メッセージを登録
      if(! $ROOM->IsOption('seal_message')) $ROOM->SystemMessage($user->handle_name, 'FOX_EAT');
      $user->wolf_killed = true; //尾行判定は成功扱い
      return true;
    }
    return false;
  }

  //人狼襲撃失敗処理
  function WolfEatSkipAction($user){}

  //妖狐襲撃処理
  function FoxEatAction($user){}

  //人狼襲撃処理
  function WolfEatAction($user){}

  //人狼襲撃死亡処理
  function WolfKill($user, $list){
    global $USERS;
    $USERS->Kill($user->user_no, 'WOLF_KILLED');
  }

  //毒対象者選出
  function GetPoisonTarget(){
    global $GAME_CONF, $USERS;
    return $GAME_CONF->poison_only_eater ? $this->GetVoter() :
      $USERS->ByUname(GetRandom($USERS->GetLivingWolves()));
  }

  //毒死処理
  function PoisonDead(){
    global $USERS;
    $USERS->Kill($this->GetActor()->user_no, 'POISON_DEAD_night');
  }
}
