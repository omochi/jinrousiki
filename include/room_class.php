<?php
class Room{
  var $id;
  var $name;
  var $comment;
  var $game_option = '';
  var $option_role = '';
  var $date;
  var $day_night;
  var $status;
  var $system_time;
  var $sudden_death;
  var $view_mode = false;
  var $dead_mode = false;
  var $heaven_mode = false;
  var $log_mode = false;
  var $test_mode = false;

  function Room($request){ $this->__construct($request); }
  function __construct($request = NULL){
    if(is_null($request)) return;
    if(isset($request->TestItems) && $request->TestItems->is_virtual_room){
      $array = $request->TestItems->test_room;
    }
    else{
      $query = "SELECT room_no AS id, room_name AS name, room_comment AS comment, ".
	"game_option, date, day_night, status FROM room WHERE room_no = {$request->room_no}";
      if(($array = FetchNameArray($query)) === false){
	OutputActionResult('エラー', '無効な村番号です：' . $request->room_no);
      }
    }
    foreach($array as $name => $value) $this->$name = $value;
    $this->ParseOption();
  }

  //シーンに合わせた投票情報を取得する
  function LoadVote($action = NULL){
    global $RQ_ARGS;

    if(isset($RQ_ARGS->TestItems) && $RQ_ARGS->TestItems->is_virtual_room){
      switch($this->day_night){
      case 'day':
	$vote_list = $RQ_ARGS->TestItems->vote_day;
	break;

      case 'night':
	$vote_list = $RQ_ARGS->TestItems->vote_night;
	break;

      default:
	return NULL;
      }
    }
    else{
      switch($this->day_night){
      case 'beforegame':
	$data = "uname, target_uname, situation";
	$action = "situation = 'GAMESTART'";
	break;

      case 'day':
	$data = "uname, target_uname, vote_number";
	$action = "situation = 'VOTE_KILL' AND vote_times = " . GetVoteTimes();
	break;

      case 'night':
	$data = "uname, target_uname, situation";
	$action = "situation <> 'VOTE_KILL'";
	break;

      default:
	return NULL;
      }
      $query = "SELECT {$data} FROM vote WHERE room_no = {$this->id} " .
	"AND date = {$this->date} AND ";
      $vote_list = FetchAssoc($query . $action);
    }

    $vote_data = array();
    foreach($vote_list as $list){
      $uname = $list['uname'];
      unset($list['uname']);
      $vote_data[$uname] = $list;
    }
    $this->vote = $vote_data;

    return count($this->vote);
  }

  function ParseOption(){
    $this->game_option = new OptionManager($this->game_option);
    $this->option_role = new OptionManager($this->role_option);
    $this->option_list = array_keys($this->game_option->options);
    if($this->IsRealTime()){
      $time_list = $this->game_option->options['real_time'];
      $this->real_time->day   = $time_list[0];
      $this->real_time->night = $time_list[1];
    }
  }

  function IsOption($option){
    return in_array($option, $this->option_list);
  }

  function IsOptionGroup($option){
    foreach($this->option_list as $this_option){
      if(strpos($this_option, $option) !== false) return true;
    }
    return false;
  }

  function IsRealTime(){
    return $this->IsOption('real_time');
  }

  function IsDummyBoy(){
    return $this->IsOption('dummy_boy');
  }

  function IsOpenCast(){
    global $USERS;

    if($this->IsOption('not_open_cast')) return false; //常時非公開
    if(! $this->IsOption('auto_open_cast')) return true; //自動公開がオフなら常時公開

    //役職をチェックしてフラグをキャッシュする
    if(is_null($this->open_cast)) $this->open_cast = $USERS->IsOpenCast();
    return $this->open_cast;
  }

  function IsQuiz(){
    return $this->IsOption('quiz');
  }

  function IsBeforeGame(){
    return $this->day_night == 'beforegame';
  }

  function IsDay(){
    return $this->day_night == 'day';
  }

  function IsNight(){
    return $this->day_night == 'night';
  }

  function IsAfterGame(){
    return $this->day_night == 'aftergame';
  }

  function IsPlaying(){
    return ($this->IsDay() || $this->IsNight());
  }

  function IsFinished(){
    return $this->status == 'finished';
  }

  //最終更新時刻を更新
  function UpdateTime(){
    if($this->test_mode) return;
    mysql_query("UPDATE room SET last_updated = '{$this->system_time}' WHERE room_no = {$this->id}");
  }
}

class RoomDataSet {
  var $rows = array();

  function LoadClosedRooms($room_order, $limit_statement) {
    $sql = <<<SQL
SELECT room.room_no AS id, room.room_name, room.room_comment, room.date AS room_date,
    room.game_option AS room_game_option, room.option_role AS room_option_role,
    room.max_user AS room_max_user, users.room_num_user, room.victory_role AS room_victory_role,
    room.establish_time, room.start_time, room.finish_time
FROM room
    LEFT JOIN (SELECT room_no, COUNT(user_no) AS room_num_user FROM user_entry GROUP BY room_no) users
	USING (room_no)
WHERE status = 'finished'
ORDER BY room_no {$room_order}
{$limit_statement}
SQL;
    return self::__load($sql);
  }

  function LoadOpeningRooms($class = 'RoomDataSet') {
    $sql = <<<SQL
SELECT room_no AS id, room_name, room_comment, game_option, option_role, max_user, status
FROM room
WHERE status <> 'finished'
ORDER BY room_no DESC
SQL;
    return self::__load($sql);
  }

  function __load($sql) {
    $result = new RoomDataSet();
    if ($q_rooms = mysql_query($sql) != null) {
      while(($object = mysql_fetch_object($q_room, $class)) !== false){
	$object->ParseCompoundParameters();
        $result->rows[] = $object;
      }
    }
    return $result;
  }
}
