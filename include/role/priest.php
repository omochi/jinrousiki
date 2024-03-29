<?php
/*
  ◆司祭 (priest)
  ○仕様
  ・司祭：村人陣営 (偶数日 / 4日目以降)
*/
class Role_priest extends Role {
  public $priest_type = 'human_side';

  //Mixin あり
  function OutputResult() {
    if (is_null($role = $this->GetOutputRole())) return;
    $this->OutputAbilityResult($this->GetEvent($role));
  }

  //司祭結果表示役職取得
  protected function GetOutputRole() {
    return DB::$ROOM->date > 3 && DB::$ROOM->date % 2 == 0 ? $this->role : null;
  }

  //イベント名取得
  protected function GetEvent($role = null) {
    return strtoupper(isset($role) ? $role : $this->role) . '_RESULT';
  }

  //司祭能力
  function Priest(StdClass $role_flag) {
    $data = $this->GetStack('priest');
    if (is_null($role = $this->GetPriestRole($data->list))) return;
    $class = $this->GetClass($method = 'GetPriestType');
    DB::$ROOM->ResultAbility($this->GetEvent($role), $data->count[$class->$method()]);
  }

  //司祭能力発動判定
  protected function GetPriestRole(array $list) {
    return DB::$ROOM->date > 2 && DB::$ROOM->date % 2 == 1 ? $this->role : null;
  }

  //司祭能力対象取得
  function GetPriestType() { return $this->priest_type; }

  //情報収集
  final function AggregatePriest(StdClass $role_flag) {
    $flag = false;
    $data = new StdClass();
    $data->list  = array();
    $data->count = array('total' => 0, 'human' => 0, 'wolf' => 0, 'fox' => 0, 'lovers' => 0,
			 'human_side' => 0, 'dead' => 0, 'dream' => 0, 'sub_role' => 0);
    $this->SetStack($data);
    foreach ($role_flag as $role => $stack) { //司祭系の出現判定
      $user = new User($role);
      if ($user->IsRoleGroup('priest')) $flag |= RoleManager::LoadMain($user)->SetPriest();
    }
    $data = $this->GetStack();
    if (DB::$ROOM->IsOption('weather') && (DB::$ROOM->date % 3) == 1) { //天候判定
      $role = 'weather_priest';
      $flag = true;
      $data->$role = true;
      if (! in_array($role, $data->list)) $data->list[] = $role;
    }
    if (! $flag) {
      $this->SetStack($data);
      return;
    }

    foreach (DB::$USER->rows as $user) { //陣営情報収集
      if ($user->IsDead(true)) {
	if (! $user->IsCamp('human', true)) $data->count['dead']++;
	continue;
      }
      $data->count['total']++;

      $dummy_user = new User($user->GetRole());
      if ($dummy_user->IsWolf()) {
	$data->count['wolf']++;
      }
      elseif ($dummy_user->IsFox()) {
	$data->count['fox']++;
      }
      else {
	$data->count['human']++;
	if ($dummy_user->IsCamp('human')) $data->count['human_side']++;
      }

      if ($user->IsLovers()) $data->count['lovers']++;

      if (in_array('dowser_priest', $data->list)) {
	$data->count['sub_role'] += count($dummy_user->role_list) - 1;
      }

      if (in_array('dummy_priest', $data->list) &&
	  ($user->IsRoleGroup('dummy') || $user->IsMainGroup('fairy'))) {
	$data->count['dream']++;
      }
    }

    if (in_array('crisis_priest', $data->list) || in_array('revive_priest', $data->list)) {
      if ($data->count['total'] - $data->count['lovers'] <= 2) {
	$data->crisis = 'lovers';
      }
      elseif ($data->count['human'] - $data->count['wolf'] <= 2 || $data->count['wolf'] == 1) {
	if ($data->count['lovers'] > 1) {
	  $data->crisis = 'lovers';
	}
	elseif ($data->count['fox'] > 0) {
	  $data->crisis = 'fox';
	}
	elseif ($data->count['human'] - $data->count['wolf'] <= 2) {
	  $data->crisis = 'wolf';
	}
      }
    }
    $this->SetStack($data);
  }

  //司祭情報セット
  protected function SetPriest() {
    $stack = $this->GetStack('priest');
    $stack->list[] = $this->role;
    $this->SetStack($stack, 'priest');
    return true;
  }
}
