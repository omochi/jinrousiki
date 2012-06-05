<?php
/*
  ◆犬神 (possessed_mad)
  ○仕様
  ・憑依無効陣営：妖狐/恋人
*/
class Role_possessed_mad extends Role {
  public $action     = 'POSSESSED_DO';
  public $not_action = 'POSSESSED_NOT_DO';
  public $ignore_message = '初日は憑依できません';

  //Mixin あり
  function OutputResult() {
    //現在の憑依先
    if (DB::$ROOM->date > 2 && ! $this->GetActor()->IsActive()) RoleHTML::OutputPossessed();
  }

  function OutputAction() {
    if ($this->GetActor()->IsActive()) {
      RoleHTML::OutputVote('wolf-eat', 'possessed_do', $this->action, $this->not_action);
    }
  }

  function IsVote() { return DB::$ROOM->date > 1; }

  function IsMindReadPossessed(User $user) { return $user->IsSame($this->GetViewer()->uname); }

  function IgnoreVote() {
    if (! is_null($str = parent::IgnoreVote())) return $str;
    return $this->GetActor()->IsActive() ? null : '能力喪失しています';
  }

  function GetVoteIconPath(User $user, $live) { return Icon::GetFile($user->icon_filename); }

  function IsVoteCheckbox(User $user, $live) {
    return ! $live && ! $this->IsActor($user->uname) && ! $user->IsDummyBoy();
  }

  function IgnoreVoteNight(User $user, $live) {
    return $live ? '死者以外には投票できません' : null;
  }

  //憑依情報セット
  function SetPossessed(User $user) {
    global $ROLES;

    foreach ($ROLES->LoadFilter('guard_curse') as $filter) { //厄神の護衛判定
      if ($filter->IsGuard($this->GetUname())) return false;
    }

    //無効判定 (蘇生/憑依制限/無効陣営/憑依済み)
    $class = $this->GetClass($method = 'IgnorePossessed');
    if ($user->revive_flag || $user->IsPossessedLimited() ||
	$class->$method($user->GetCamp(true)) ||
	! DB::$USER->ByRealUname($user->uname)->IsSame($user->uname)) {
      return false;
    }
    $this->AddStack($user->uname, 'possessed_dead');
  }

  //無効陣営判定
  function IgnorePossessed($camp) { return $camp == 'fox' || $camp == 'lovers'; }

  //憑依情報登録
  function Possessed() {
    $stack = $this->GetStack('possessed_dead');
    foreach ($stack as $uname => $target_uname) {
      if (count(array_keys($stack, $target_uname)) == 1) { //競合判定
	$this->AddStack($target_uname, 'possessed', $uname);
      }
    }
  }
}
