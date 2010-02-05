<?php
class Room {
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

  function __construct($request = null){
    if(!empty($request)) {
      if(isset($request->TestItems) && $request->TestItems->is_virtual_room){
        $array = $request->TestItems->test_room;
      }
      else{
        $query = "SELECT room_no, room_name, room_comment, game_option, date, day_night, status " .
  	"FROM room WHERE room_no = {$request->room_no}";
        if(($array = FetchNameArray($query)) === false) return false;
      }
      $this->LoadArray($array);
    }
    $this->ParseCompoundParameters();
  }
  function Room($request = null){
    self::__construct($request);
  }

  function LoadArray($array) {
    foreach($array as $name => $value){
      $this->$name = $value;
    }
    $this->id = $this->room_no;
  }

  function ParseCompoundParameters() {
    $this->game_option = new OptionManager($this->game_option);
    $this->option_role = new OptionManager($this->role_option);
  }

  function IsOption($option){
    return in_array($option, $this->option_list);
  }

  function IsOptionGroup($option){
    return (strpos($this->game_option, $option) !== false);
  }

  function IsRealTime(){
    return $this->IsOptionGroup('real_time');
  }

  function IsDummyBoy(){
    return $this->IsOption('dummy_boy');
  }

  function IsOpenCast(){
    return ! $this->IsOption('not_open_cast');
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