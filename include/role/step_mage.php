<?php
/*
  ◆審神者 (step_mage)
  ○仕様
*/
RoleManager::LoadFile('mage');
class Role_step_mage extends Role_mage {
  const CHECKBOX = "<input type=\"checkbox\" name=\"target_no[]\" id=\"%d\" value=\"%d\">\n";
  public $action = 'STEP_MAGE_DO';
  public $submit = 'mage_do';

  function GetVoteCheckbox(User $user, $id, $live) {
    return $this->IsActor($user->uname) ? '' : sprintf(self::CHECKBOX, $id, $id);
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
    foreach ($root_list as $id) {
      $user = DB::$USER->ByID($id);
      $target_stack[$id] = DB::$USER->ByReal($id)->user_no;
      $handle_stack[$id] = $user->handle_name;
    }

    $this->SetStack(implode(' ', $target_stack), 'target_no');
    $this->SetStack(implode(' ', $handle_stack), 'target_handle');
    return null;
  }

  function Mage(User $user) {
    if (! parent::Mage($user)) return false;
    $stack  = $this->GetStack('step');
    $result = array();
    foreach ($stack[$this->GetActor()->user_no] as $id) {
      if (DB::$USER->IsVirtualLive($id)) $result[] = $id;
    }
    if (count($result) < 1) return true;
    return DB::$ROOM->ResultDead(implode(' ', $result), 'STEP');
  }

  //隣り合っている ID を取得
  function GetChain($id, $max) {
    $stack = array();
    if ($id - 5 > 1)     $stack['U'] = $id - 5;
    if ($id + 5 <= $max) $stack['D'] = $id + 5;
    if ((($id - 1) % 5) != 0 && $id > 1)    $stack ['L'] = $id - 1;
    if ((($id + 1) % 5) != 1 && $id < $max) $stack ['R'] = $id + 1;
    return $stack;
  }
}
