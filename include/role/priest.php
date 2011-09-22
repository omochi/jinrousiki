<?php
/*
  ◆司祭 (priest)
  ○仕様
  ・司祭：村人陣営
  ・結果表示：偶数日 (4日目以降)
*/
class Role_priest extends Role{
  public $display_role = NULL;
  public $result_date  = 'even';
  public $priest_type  = 'human_side';

  function __construct(){
    parent::__construct();
    if(is_null($this->display_role)) $this->display_role = $this->role;
  }

  //司祭能力
  function Priest($role_flag, $data){
    global $ROOM;

    switch($this->result_date){
    case 'even':
      $flag = $ROOM->date > 2 && ($ROOM->date % 2) == 1;
      break;

    case 'odd':
      $flag = $ROOM->date > 3 && ($ROOM->date % 2) == 0;
      break;

    case 'both':
      $role = ($ROOM->date % 2) == 1 ? 'priest' : 'bishop_priest';
      $flag = $ROOM->date > 3 && ! in_array($role, $data->list);
      break;

    default:
      $flag = false;
      break;
    }
    if(! $flag) return;
    $result = $data->count[isset($type) ? $type : $this->priest_type];
    $ROOM->SystemMessage($result, $this->GetEvent($role));
  }

  //結果表示
  function OutputResult(){
    global $ROOM;

    switch($this->result_date){
    case 'even':
      $flag = $ROOM->date > 3 && ($ROOM->date % 2) == 0;
      break;

    case 'odd':
      $flag = $ROOM->date > 2 && ($ROOM->date % 2) == 1;
      break;

    case 'both':
      $role = ($ROOM->date % 2) == 0 ? 'priest' : 'bishop_priest';
      $flag = $ROOM->date > 4;
      break;

    case 'second':
      $flag = $ROOM->date > 1;
      break;

    case 'third':
      $flag = $ROOM->date > 2;
      break;

    default:
      $flag = false;
      break;
    }
    if($flag) OutputSelfAbilityResult($this->GetEvent($role));
  }

  //イベント名取得
  function GetEvent($role = NULL){
    return strtoupper(isset($role) ? $role : $this->role) . '_RESULT';
  }

  //情報収集
  function AggregatePriest($role_flag, &$data){
    global $ROOM, $USERS;

    $flag = false;
    $data->list  = array();
    $data->count = array('total' => 0, 'human' => 0, 'wolf' => 0, 'fox' => 0, 'lovers' => 0,
			 'human_side' => 0, 'dead' => 0, 'dream' => 0, 'sub_role' => 0);
    foreach($role_flag as $role => $stack){ //司祭系の出現判定
      $user = new User($role);
      if(! $user->IsRoleGroup('priest') ||
	 ($user->IsRole('dummy_priest') && $ROOM->IsEvent('no_dream'))) continue;
      $data->list[] = $role;
      $flag |= ! $user->IsRole('widow_priest', 'border_priest');
    }
    if($ROOM->IsOption('weather') && ($ROOM->date % 3) == 1){ //天候判定
      $flag = true;
      $data->weather = true;
      if(! in_array('weather_priest', $data->list)) $data->list[] = 'weather_priest';
    }
    if(! $flag) return $data;

    foreach($USERS->rows as $user){ //陣営情報収集
      if($user->IsDead(true)){
	if(! $user->IsCamp('human', true)) $data->count['dead']++;
	continue;
      }
      $data->count['total']++;

      if($user->IsWolf()){
	$data->count['wolf']++;
      }
      elseif($user->IsFox()){
	$data->count['fox']++;
      }
      else{
	$data->count['human']++;
	if($user->IsCamp('human')) $data->count['human_side']++;
      }

      if($user->IsLovers()) $data->count['lovers']++;

      if(in_array('dowser_priest', $data->list)){
	$dummy_user = new User();
	$dummy_user->ParseRoles($user->GetRole());
	$data->count['sub_role'] += count($dummy_user->role_list) - 1;
      }

      if(in_array('dummy_priest', $data->list) && $user->IsRoleGroup('dummy', 'fairy')){
	$data->count['dream']++;
      }
    }

    if(in_array('crisis_priest', $data->list) || in_array('revive_priest', $data->list)){
      if($data->count['total'] - $data->count['lovers'] <= 2){
	$data->crisis = 'lovers';
      }
      elseif($data->count['human'] - $data->count['wolf'] <= 2 || $data->count['wolf'] == 1){
	if($data->count['lovers'] > 1)
	  $data->crisis = 'lovers';
	elseif($data->count['fox'] > 0)
	  $data->crisis = 'fox';
	elseif($data->count['human'] - $data->count['wolf'] <= 2)
	  $data->crisis = 'wolf';
      }
    }
    return $data;
  }
}
