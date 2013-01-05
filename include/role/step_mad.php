<?php
/*
  ◆家鳴 (step_mad)
  ○仕様
*/
class Role_step_mad extends Role {
  public $action     = 'STEP_DO';
  public $not_action = 'STEP_NOT_DO';
  public $checkbox = '<input type="checkbox" name="target_no[]"';

  function OutputAction() {
    RoleHTML::OutputVote('fairy-do', 'step_do', $this->action, $this->not_action);
  }

  function IsVoteCheckbox(User $user, $live) { return true; }

  function VoteNight() {
    $stack = $this->GetVoteNightTarget();
    //Text::p($stack);
    sort($stack);

    $id  = array_shift($stack);
    $max = count(DB::$USER->rows);

    $last_vector = null;
    $count       = 0;
    $root_list   = array($id);
    while (count($stack) > 0) {
      $chain = $this->GetChain($id, $max);
      $point = array_intersect($chain, $stack);
      if (count($point) != 1) return '通り道が一本に繋がっていません';

      $vector = array_shift(array_keys($point));
      if ($vector != $last_vector) {
	if ($count++ > 0) return '通り道は直線にしてください';
	$last_vector = $vector;
      }

      $id = array_shift($point);
      $root_list[] = $id;
      unset($stack[array_search($id, $stack)]);
    }

    $target_stack = array();
    $handle_stack = array();
    foreach ($root_list as $id) {
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
    sort($list);
    $result = array();
    foreach ($list as $id) {
      if (DB::$USER->IsVirtualLive($id)) $result[] = $id;
    }
    if (count($result) < 1) return true;
    return DB::$ROOM->ResultDead(implode(' ', $result), 'STEP');
  }
}
