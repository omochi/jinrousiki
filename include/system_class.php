<?php
class RoomDataSet{
  var $id;
  var $name;
  var $comment;
  var $game_option;
  var $date;
  var $day_night;
  var $status;
  var $system_time;
  var $sudden_death;
  var $view_mode;
  var $dead_mode;
  var $heaven_mode;
  var $log_mode;

  function RoomDataSet($room_no, $debug = false){
    if($debug) return;
    $sql = mysql_query("SELECT room_name, room_comment, game_option, date, day_night, status
			FROM room WHERE room_no = $room_no");
    $array = mysql_fetch_assoc($sql);
    $this->id          = $room_no;
    $this->name        = $array['room_name'];
    $this->comment     = $array['room_comment'];
    $this->game_option = $array['game_option'];
    $this->date        = $array['date'];
    $this->day_night   = $array['day_night'];
    $this->status      = $array['status'];
    $this->view_mode   = false;
    $this->dead_mode   = false;
    $this->heaven_mode = false;
    $this->log_mode    = false;
  }

  function is_real_time(){
    return (strpos($this->game_option, 'real_time') !== false);
  }

  function is_dummy_boy(){
    return (strpos($this->game_option, 'dummy_boy') !== false);
  }

  function is_open_cast(){
    return (strpos($this->game_option, 'not_open_cast') === false);
  }

  function is_quiz(){
    return (strpos($this->game_option, 'quiz') !== false);
  }

  function is_beforegame(){
    return $this->day_night == 'beforegame';
  }

  function is_day(){
    return $this->day_night == 'day';
  }

  function is_night(){
    return $this->day_night == 'night';
  }

  function is_aftergame(){
    return $this->day_night == 'aftergame';
  }

  function is_playing(){
    return ($this->is_day() || $this->is_night());
  }

  function is_finished(){
    return $this->status == 'finished';
  }
}

//画像管理クラスの基底クラス
class ImageManager{
  function GenerateTag($name, $alt = ''){
    $str = '<img';
    if($this->class != '') $str .= ' class="' . $this->class . '"';
    $str .= ' src="' . $this->path . '/' . $name . '.' . $this->extention . '"';
    if($alt != ''){
      EscapeStrings(&$alt);
      $str .= ' alt="' . $alt . '" title="' . $alt . '"';
    }
    return $str . '>';
  }
}

//村のオプション画像情報
class RoomImage extends ImageManager{
  var $path      = 'img/room_option';
  var $extention = 'gif';
  var $class     = 'option';
  /*
  //村の最大人数リスト (RoomConfig -> max_user_list と連動させる)
  var $max_user_list = array(
			      8 => 'img/room_option/max8.gif',   // 8人
			     16 => 'img/room_option/max16.gif',  //16人
			     22 => 'img/room_option/max22.gif'   //22人
			     );
  */
}
$ROOM_IMG = new RoomImage();

//役職の画像情報
class RoleImage extends ImageManager{
  var $path      = 'img/role';
  var $extention = 'gif';
  var $class     = '';

  function DisplayImage($name){
    echo $this->GenerateTag($name) . '<br>'."\n";
  }
}

//勝利陣営の画像情報
class VictoryImage extends ImageManager{
  var $path      = 'img/victory_role';
  var $extention = 'jpg';
  var $class     = 'winner';

  function MakeVictoryImage($victory_role){
    $name = $victory_role;
    switch($victory_role){
    case 'human':
      $alt = '村人勝利';
      break;

    case 'wolf':
      $alt = '人狼勝利';
      break;

    case 'fox1':
    case 'fox2':
      $name = 'fox';
      $alt = '妖狐勝利';
      break;

    case 'lovers':
      $alt = '恋人勝利';
      break;

    case 'quiz':
      $alt = '出題者勝利';
      break;

    case 'draw':
    case 'vanish':
    case 'quiz_dead':
      $name = 'draw';
      $alt = '引き分け';
      break;

    default:
      return '-';
      break;
    }
    return $this->GenerateTag($name, $alt);
  }
}

//音源パス
class Sound{
  var $morning          = 'swf/sound_morning.swf';          //夜明け
  var $revote           = 'swf/sound_revote.swf';           //再投票
  var $objection_male   = 'swf/sound_objection_male.swf';   //異議あり(男)
  var $objection_female = 'swf/sound_objection_female.swf'; //異議あり(女)
}
?>
