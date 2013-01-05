<?php
/*
  ◆響狼 (step_wolf)
  ○仕様
*/
RoleManager::LoadFile('wolf');
class Role_step_wolf extends Role_wolf {
  public $action     = 'STEP_WOLF_EAT';
  public $add_action = 'SILENT_WOLF_EAT';
  public $submit     = 'wolf_eat';
  public $checkbox   = '<input type="checkbox" name="target_no[]"';

  protected function OutputResult() {
    if (! DB::$ROOM->IsNight()) return;
    $count = max(0, 2 - (int)$this->GetActor()->GetMainRoleTarget());
    RoleHTML::OutputAbilityResult('ability_' . $this->role, $count);
  }

  function IsFinishVote(array $list) {
    if ($this->IsSilentVote()) unset($list[$this->not_action]);
    return parent::IsFinishVote($list);
  }

  private function IsSilentVote() {
    return (int)$this->GetActor()->GetMainRoleTarget() < 2;
  }

  function IsVoteCheckbox(User $user, $live) { return ! $this->IsActor($user->uname); }

  function SetVoteNight() {
    parent::SetVoteNight();
    if ($this->IsDummyBoy() || ! $this->IsSilentVote()) {
      $this->SetStack(null, 'add_action');
    }
  }

  function VoteNight() {
    $stack = $this->GetVoteNightTarget();
    //Text::p($stack);

    $actor = $this->GetActor();
    $id  = $actor->user_no;
    $max = count(DB::$USER->rows);

    $last_vector = null;
    $count       = 0;
    $root_list   = array();
    if ($this->IsDummyBoy()) { //身代わり君襲撃モード
      $id = array_shift($stack);
      if (! DB::$USER->ByID($id)->IsDummyBoy()) { //身代わり君判定
	return DB::$ROOM->IsQuiz() ? 'クイズ村では GM 以外に投票できません' :
	  '身代わり君使用の場合は、身代わり君以外に投票できません';
      }
      if (count($stack) > 0) return '通り道が一本に繋がっていません';
      $root_list[] = $id;
    } else {
      do {
	$chain = $this->GetChain($id, $max);
	$point = array_intersect($chain, $stack);
	if (count($point) != 1) return '通り道が一本に繋がっていません';

	$vector = array_shift(array_keys($point));
	if ($vector != $last_vector) {
	  if ($count++ > 1) return '方向転換は一回まで';
	  $last_vector = $vector;
	}

	$id = array_shift($point);
	$root_list[] = $id;
	unset($stack[array_search($id, $stack)]);
      } while (count($stack) > 0);
    }
    if (count($root_list) < 1) return '通り道が自分と繋がっていません';

    $user = DB::$USER->ByID($id);
    $live = DB::$USER->IsVirtualLive($user->user_no); //仮想的な生死を判定
    if (! is_null($str = parent::IgnoreVoteNight($user, $live))) return $str;

    $target_stack = array();
    $handle_stack = array();
    foreach ($root_list as $id) { //投票順に意味があるので sort しない
      $user = DB::$USER->ByID($id);
      $target_stack[$id] = DB::$USER->ByReal($id)->user_no;
      $handle_stack[$id] = $user->handle_name;
    }

    $this->SetStack(implode(' ', $target_stack), 'target_no');
    $this->SetStack(implode(' ', $handle_stack), 'target_handle');
    return null;
  }

  //足音処理
  function Step(array $list) {
    array_pop($list); //最後尾は対象者なので除く
    sort($list);
    $result = array();
    foreach ($list as $id) {
      if (DB::$USER->IsVirtualLive($id)) $result[] = $id;
    }
    if (count($result) < 1) return true;
    return DB::$ROOM->ResultDead(implode(' ', $result), 'STEP');
  }

  //ステルス襲撃回数更新
  function UpdateStep() {
    $role  = $this->role;
    $times = (int)$this->GetActor()->GetMainRoleTarget();
    if ($times > 0) $role .= sprintf('[%d]', $times);
    $this->GetActor()->ReplaceRole($role, sprintf('%s[%d]', $this->role, $times + 1));
  }
}
