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

  function Room($request = NULL){ $this->__construct($request); }
  function __construct($request = NULL){
    if(is_null($request)) return;
    if($request->IsVirtualRoom()){
      $array = $request->TestItems->test_room;
      $this->event->rows = $request->TestItems->event;
    }
    else{
      $array = $this->LoadRoom($request->room_no);
    }
    foreach($array as $name => $value) $this->$name = $value;
    $this->ParseOption();
  }

  //指定した部屋番号の DB 情報を取得する
  function LoadRoom($room_no){
    $query = 'SELECT room_no AS id, room_name AS name, room_comment AS comment, ' .
      'game_option, date, day_night, status FROM room WHERE room_no = ' . $room_no;
    $array = FetchAssoc($query, true);
    if(count($array) < 1) OutputActionResult('村番号エラー', '無効な村番号です: ' . $room_no);
    return $array;
  }

  //option_role を追加ロードする
  function LoadOption(){
    global $RQ_ARGS;

    $option_role = $RQ_ARGS->IsVirtualRoom() ? $RQ_ARGS->TestItems->test_room['option_role'] :
      FetchResult('SELECT option_role FROM room' . $this->GetQuery(false));
    $this->option_role = new OptionManager($option_role);
    $this->option_list = array_merge($this->option_list, array_keys($this->option_role->options));
  }

  //発言を取得する
  function LoadTalk($heaven = false){
    global $GAME_CONF;

    $query = 'SELECT uname, sentence, font_type, location FROM talk' . $this->GetQuery(! $heaven) .
      ' AND location LIKE ' . ($heaven ? "'heaven'" : "'{$this->day_night}%'") .
      ' ORDER BY talk_id DESC';
    if(! $this->IsPlaying()) $query .= ' LIMIT 0, ' . $GAME_CONF->display_talk_limit;
    return FetchObject($query, 'Talk');
  }

  //シーンに合わせた投票情報を取得する
  function LoadVote($kick = false){
    global $RQ_ARGS;

    if($RQ_ARGS->IsVirtualRoom()){
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
	if($kick){
	  $data = 'uname, target_uname';
	  $action = "situation = 'KICK_DO'";
	}
	else{
	  $data = 'uname, target_uname, situation';
	  $action = "situation = 'GAMESTART'";
	}
	break;

      case 'day':
	$data = 'uname, target_uname, vote_number';
	$action = "situation = 'VOTE_KILL' AND vote_times = " . $this->GetVoteTimes();
	break;

      case 'night':
	$data = 'uname, target_uname, situation';
	$action = "situation <> 'VOTE_KILL'";
	break;

      default:
	return NULL;
      }
      $vote_list = FetchAssoc("SELECT {$data} FROM vote {$this->GetQuery()} AND {$action}");
    }

    $stack = array();
    if($kick){
      foreach($vote_list as $list) $stack[$list['uname']][] = $list['target_uname'];
    }
    else{
      foreach($vote_list as $list){
	$uname = $list['uname'];
	unset($list['uname']);
	$stack[$uname] = $list;
      }
    }
    $this->vote = $stack;

    return count($this->vote);
  }

  //投票回数を DB から取得する
  function LoadVoteTimes($revote = false){
    $query = 'SELECT message FROM system_message' . $this->GetQuery() . ' AND type = ' .
      ($revote ?  "'RE_VOTE' ORDER BY message DESC" : "'VOTE_TIMES'");
    return (int)FetchResult($query);
  }

  //特殊イベント判定用の情報を DB から取得する
  function LoadEvent(){
    $query = 'SELECT message, type FROM system_message' . $this->GetQuery(false) . ' AND date = '  .
      ($this->date - 1) . " AND(type = 'WOLF_KILLED' OR type = 'VOTE_KILLED')";
    $this->event->rows = FetchAssoc($query);
  }

  function ParseOption($join = false){
    $this->game_option = new OptionManager($this->game_option);
    $this->option_role = new OptionManager($this->option_role);
    $this->option_list = array_merge(array_keys($this->game_option->options),
				     $join ? array_keys($this->option_role->options) : array());
    if($this->IsRealTime()){
      $time_list = $this->game_option->options['real_time'];
      $this->real_time->day   = $time_list[0];
      $this->real_time->night = $time_list[1];
    }
  }

  //投票情報をコマンド毎に分割する
  function ParseVote(){
    $stack = array();
    foreach($this->vote as $uname => $list){
      extract($list);
      $stack[$situation][$uname] = $target_uname;
    }
    return $stack;
  }

  //共通クエリを取得
  function GetQuery($date = true, $count = NULL){
    $query = (is_null($count) ? '' : 'SELECT COUNT(uname) FROM ' . $count) .
      ' WHERE room_no = ' . $this->id;
    return $date ? $query . ' AND date = ' . $this->date : $query;
  }

  //投票回数を取得する
  function GetVoteTimes($revote = false){
    $value = $revote ? 'revote_times' : 'vote_times';
    if(is_null($this->$value)) $this->$value = $this->LoadVoteTimes($revote);
    return $this->$value;
  }

  //特殊イベント判定用の情報を取得する
  function GetEvent($force = false){
    if(! $this->IsPlaying()) return array();
    if($force || is_null($this->event)) $this->LoadEvent();
    return $this->event->rows;
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
    return $this->IsDay() || $this->IsNight();
  }

  function IsFinished(){
    return $this->status == 'finished';
  }

  function IsEvent($type){
    return $this->event->$type;
  }

  //最終更新時刻を更新
  function UpdateTime(){
    if($this->test_mode) return;
    SendQuery('UPDATE room SET last_updated = UNIX_TIMESTAMP()' . $this->GetQuery(false));
  }

  //発言登録
  function Talk($sentence, $uname = '', $location = '', $font_type = NULL, $spend_time = 0){
    if(empty($uname)) $uname = 'system';
    if(empty($location)) $location = $this->day_night . ' system';
    if($this->test_mode){
      PrintData($sentence, 'Talk: ' . $uname . ': '. $location);
      return;
    }

    $items  = 'room_no, date, location, uname, sentence, spend_time, time';
    $values = "{$this->id}, {$this->date}, '{$location}', '{$uname}', '{$sentence}', " .
      "{$spend_time}, UNIX_TIMESTAMP()";
    if(isset($font_type)){
      $items .= ', font_type';
      $values .= ", '{$font_type}'";
    }
    return InsertDatabase('talk', $items, $values);
  }

  //システムメッセージ登録
  function SystemMessage($sentence, $type){
    if($this->test_mode){
      PrintData($sentence, 'SystemMessage: ' . $type);
      return;
    }
    $items = 'room_no, date, message, type';
    $values = "{$this->id}, {$this->date}, '{$sentence}', '{$type}'";
    return InsertDatabase('system_message', $items, $values);
  }

  //次の日にする
  function ChangeDate(){
    $this->date++;
    $this->day_night = 'day';
    SendQuery("UPDATE room SET date = {$this->date}, day_night = 'day' WHERE room_no = {$this->id}");

    //夜が明けた通知
    $this->Talk("MORNING\t" . $this->date);
    $this->SystemMessage(1, 'VOTE_TIMES'); //処刑投票のカウントを 1 に初期化(再投票で増える)
    $this->UpdateTime(); //最終書き込みを更新
    //DeleteVote(); //今までの投票を全部削除

    CheckVictory(); //勝敗のチェック
    SendCommit(); //一応コミット
  }
}

class RoomDataSet{
  var $rows = array();

  function LoadFinishedRoom($room_no){
    $query = <<<EOF
SELECT room_no AS id, room_name AS name, room_comment AS comment, date, game_option,
  option_role, max_user, victory_role, establish_time, start_time, finish_time,
  (SELECT COUNT(user_no) FROM user_entry WHERE user_entry.room_no = room.room_no
   AND user_entry.user_no > 0) AS user_count
FROM room WHERE status = 'finished' AND room_no = {$room_no}
EOF;
    return FetchObject($query, 'Room', true);
  }

  function LoadEntryUser($room_no){
    $query = <<<EOF
SELECT room_no AS id, date, day_night, status, max_user FROM room WHERE room_no = {$room_no}
EOF;
    return FetchObject($query, 'Room', true);
  }

  function LoadEntryUserPage($room_no){
    $query = <<<EOF
SELECT room_no AS id, room_name AS name, room_comment AS comment, status,
  game_option, option_role FROM room WHERE room_no = {$room_no}
EOF;
    return FetchObject($query, 'Room', true);
  }

  function LoadClosedRooms($room_order, $limit_statement) {
    $sql = <<<SQL
SELECT room.room_no AS id, room.room_name AS name, room.room_comment AS comment,
    room.date AS room_date AS date, room.game_option AS room_game_option,
    room.option_role AS room_option_role, room.max_user AS room_max_user, users.room_num_user,
    room.victory_role AS room_victory_role, room.establish_time, room.start_time, room.finish_time
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
SELECT room_no AS id, room_name AS name, room_comment AS comment, game_option, option_role, max_user, status
FROM room
WHERE status <> 'finished'
ORDER BY room_no DESC
SQL;
    return self::__load($sql);
  }

  function __load($sql, $class = 'Room') {
    $result = new RoomDataSet();
    if (($q_rooms = mysql_query($sql)) !== false) {
      while(($object = mysql_fetch_object($q_rooms, $class)) !== false){
        $object->ParseOption();
        $result->rows[] = $object;
      }
    }
    else {
      die('村一覧の取得に失敗しました');
    }
    return $result;
  }
}
