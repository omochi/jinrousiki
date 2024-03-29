<?php
/*
  ◆キューピッド (cupid)
  ○仕様
  ・仲間表示：自分が矢を打った恋人 (自分自身含む)
  ・追加役職：なし
*/
class Role_cupid extends Role {
  public $action = 'CUPID_DO';
  public $self_shoot  = false;
  public $shoot_count = 2;

  protected function OutputPartner() {
    $id = $this->GetID();
    $stack = array();
    foreach (DB::$USER->rows as $user) {
      if ($user->IsPartner('lovers', $id)) $stack[] = $user->handle_name;
    }
    RoleHTML::OutputPartner($stack, 'cupid_pair');
  }

  function OutputAction() {
    RoleHTML::OutputVote('cupid-do', 'cupid_do', $this->action);
  }

  function IsVote() { return DB::$ROOM->IsDate(1); }

  function GetIgnoreMessage() { return '初日以外は投票できません'; }

  function SetVoteNightFilter() {
    $this->SetStack(DB::$USER->GetUserCount() < GameConfig::CUPID_SELF_SHOOT, 'self_shoot');
  }

  function IsVoteCheckbox(User $user, $live) { return $live && ! $user->IsDummyBoy(); }

  function IsVoteCheckboxChecked(User $user) {
    return $this->IsSelfShoot() && $this->IsActor($user);
  }

  function GetVoteCheckboxHeader() { return '<input type="checkbox" name="target_no[]"'; }

  //自分撃ち判定
  function IsSelfShoot() { return $this->GetStack('self_shoot') || $this->self_shoot; }

  function VoteNight() {
    $stack = $this->GetVoteNightTarget();
    //人数チェック
    $count = $this->GetVoteNightTargetCount();
    if (count($stack) != $count) return sprintf('指定人数は %d 人にしてください', $count);

    $self_shoot = false; //自分撃ちフラグ
    $user_list  = array();
    sort($stack);
    foreach ($stack as $id) {
      $user = DB::$USER->ByID($id);
      //例外判定
      if ($user->IsDead() || $user->IsDummyBoy()) return '死者と身代わり君には投票できません';
      $user_list[$id] = $user;
      $self_shoot |= $this->IsActor($user); //自分撃ち判定
    }

    if (! $self_shoot) { //自分撃ちエラー判定
      $str = '必ず自分を対象に含めてください';
      if ($this->self_shoot)    return $str; //自分撃ち固定役職
      if ($this->IsSelfShoot()) return '少人数村の場合は、' . $str; //参加人数
    }
    $class = $this->GetClass($method = 'VoteNightAction');
    $class->$method($user_list, $self_shoot);
    return null;
  }

  //投票人数取得
  function GetVoteNightTargetCount() { return $this->shoot_count; }

  //キューピッドの投票処理
  function VoteNightAction(array $list, $flag) {
    $role  = $this->GetActor()->GetID('lovers');
    $stack = array();
    foreach ($list as $user) {
      $stack[] = $user->handle_name;
      $user->AddRole($role); //恋人セット
      $this->AddCupidRole($user, $flag); //役職追加
      $user->Reparse(); //再パース (魂移使判定用：反映が保障されているのは恋人のみ)
    }
    $this->SetStack(implode(' ', array_keys($list)), 'target_no');
    $this->SetStack(implode(' ', $stack), 'target_handle');
  }

  //役職追加処理
  protected function AddCupidRole(User $user, $flag) {}
}
