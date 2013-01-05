<?php
/*
  ◆山立 (step_guard)
  ○仕様
  ・護衛失敗：通常
  ・護衛処理：なし
  ・狩り：通常
*/
RoleManager::LoadFile('guard');
class Role_step_guard extends Role_guard {
  public $action = 'STEP_GUARD_DO';
  public $submit = 'guard_do';
  public $checkbox = '<input type="checkbox" name="target_no[]"';

  function IsVoteCheckbox(User $user, $live) { return ! $this->IsActor($user->uname); }

  function VoteNight() {
    $stack = $this->GetVoteNightTarget();
    //Text::p($stack);

    $actor = $this->GetActor();
    $id  = $actor->user_no;
    $max = count(DB::$USER->rows);

    $last_vector = null;
    $count       = 0;
    $root_list   = array();
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
    if (count($root_list) < 1) return '通り道が自分と繋がっていません';

    $target = DB::$USER->ByID($id);
    if ($this->IsActor($target->uname) || ! DB::$USER->IsVirtualLive($id)) { //例外判定
      return '自分・死者には投票できません';
    }

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
}
