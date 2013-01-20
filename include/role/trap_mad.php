<?php
/*
  ◆罠師 (trap_mad)
  ○仕様
  ・罠：罠死
*/
class Role_trap_mad extends Role {
  public $action     = 'TRAP_MAD_DO';
  public $not_action = 'TRAP_MAD_NOT_DO';
  public $submit     = 'trap_do';
  public $not_submit = 'trap_not_do';
  public $ignore_message = '初日は罠を設置できません';
  public $trap_action = 'trap';
  public $trap_result = 'trapped';

  function OutputAction() {
    if ($this->IsVoteTrap()) {
      RoleHTML::OutputVote('wolf-eat', $this->submit, $this->action, $this->not_action);
    }
  }

  function IsVote() { return DB::$ROOM->date > 1; }

  //罠能力判定
  protected function IsVoteTrap() { return $this->GetActor()->IsActive(); }

  function IsFinishVote(array $list) {
    return ! $this->IsVoteTrap() || parent::IsFinishVote($list);
  }

  function IgnoreVote() {
    if (! is_null($str = parent::IgnoreVote())) return $str;
    return $this->IsVoteTrap() ? null : '能力喪失しています';
  }

  function IsVoteCheckbox(User $user, $live) { return $live; }

  function IgnoreVoteNight(User $user, $live) { return $live ? null : '死者には投票できません'; }

  //罠設置
  function SetTrap(User $user) {
    //人狼に狙われていたら自己設置以外は無効
    if ($this->IsActor($this->GetWolfTarget()) && ! $this->IsActor($user)) return;
    $this->AddStack($user->id, $this->trap_action);
    $this->SetTrapAction();
  }

  //罠設置後処理
  protected function SetTrapAction() { $this->GetActor()->LostAbility(); }

  //罠能力者の罠判定
  function TrapToTrap() {
    //同種罠判定 (自分自身以外に罠を仕掛けた場合、設置先に罠があった場合は罠にかかる)
    $stack = $this->GetStack($this->trap_action);
    $count = array_count_values($stack);
    foreach ($stack as $id => $target_id) {
      if ($id != $target_id && $count[$target_id] > 1) {
	$this->AddSuccess($id, $this->trap_result);
      }
    }

    //他種罠能力者の罠判定
    foreach($this->GetStack($this->GetOtherTrap()) as $id => $target_id) {
      if ($id != $target_id && in_array($target_id, $stack)) {
	$this->AddSuccess($id, $this->trap_result);
      }
    }
  }

  //他種罠タイプ取得
  protected function GetOtherTrap() { return 'snow_trap'; }

  //罠死判定
  function TrapKill(User $user, $id) {
    if ($flag = $this->IsTrap($id)) DB::$USER->Kill($user->id, 'TRAPPED');
    return $flag;
  }

  //罠死リスト判定
  function DelayTrap(User $user, $id) {
    if ($flag = $this->IsTrap($id)) $this->AddSuccess($user->id, $this->trap_result);
    return $flag;
  }

  //罠判定
  protected function IsTrap($id) {
    return in_array($id, $this->GetStack($this->trap_action));
  }

  //罠死+凍傷リスト判定
  function TrapStack(User $user, $id) { return $this->TrapKill($user, $id); }

  //罠死リストの死亡処理
  function DelayTrapKill() {
    foreach ($this->GetStack($this->trap_result) as $id => $flag) {
      DB::$USER->Kill($id, 'TRAPPED');
    }
    $this->SetStack(array(), $this->trap_result); //リストをリセット
  }
}
