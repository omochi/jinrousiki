<?php
//-- データベース処理の規定クラス --//
class DatabaseConfigBase{
  //データベース接続
  /*
    $header : HTMLヘッダ出力情報 [true: 出力済み / false: 未出力]
    $exit   : エラー処理 [true: exit を返す / false で終了]
  */
  function Connect($header = false, $exit = true){
    //データベースサーバにアクセス
    $db_handle = mysql_connect($this->host, $this->user, $this->password);
    if($db_handle){ //アクセス成功
      mysql_set_charset('ujis');
      if(mysql_select_db($this->name, $db_handle)){ //データベース接続
	//mysql_query("SET NAMES utf8");
	//成功したらハンドルを返して処理終了
	$this->db_handle = $db_handle;
	return $db_handle;
      }
      else{
	$error_title = 'データベース接続失敗';
	$error_name  = $this->name;
      }
    }
    else{
      $error_title = 'MySQLサーバ接続失敗';
      $error_name  = $this->host;
    }

    $error_message = $error_title . ': ' . $error_name; //エラーメッセージ作成
    if($header){
      echo '<font color="#FF0000">' . $error_message . '</font><br>';
      if($exit) OutputHTMLFooter($exit);
      return false;
    }
    OutputActionResult($error_title, $error_message);
  }

  //データベースとの接続を閉じる
  function Disconnect($unlock = false){
    if(is_null($this->db_handle)) return;

    if($unlock) mysql_query('UNLOCK TABLES'); //ロック解除
    mysql_close($this->db_handle);
    $this->db_handle = NULL; //ハンドルをクリア
  }
}

class RoomDataSet{
  var $id;
  var $name;
  var $comment;
  var $game_option;
  var $date;
  var $day_night;
  var $status;
  var $option_list = array();
  var $system_time;
  var $sudden_death;
  var $view_mode = false;
  var $dead_mode = false;
  var $heaven_mode = false;
  var $log_mode = false;
  var $test_mode = false;

  function RoomDataSet($request){ $this->__construct($request); }

  function __construct($request){
    if(isset($request->TestItems) && $request->TestItems->is_virtual_room){
      $array = $request->TestItems->test_room;
    }
    else{
      $query = "SELECT room_name, room_comment, game_option, date, day_night, status " .
	"FROM room WHERE room_no = {$request->room_no}";
      if(($array = FetchNameArray($query)) === false){
	OutputActionResult('エラー', '無効な村番号です：' . $request->room_no);
      }
    }
    $this->id          = $request->room_no;
    $this->name        = $array['room_name'];
    $this->comment     = $array['room_comment'];
    $this->game_option = $array['game_option'];
    $this->date        = $array['date'];
    $this->day_night   = $array['day_night'];
    $this->status      = $array['status'];
    $this->option_list = explode(' ', $this->game_option);
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

//-- クッキーデータのロード処理 --//
class CookieDataSet{
  var $day_night;  //夜明けを音でしらせるため
  var $vote_times; //再投票を音で知らせるため
  var $objection;  //「異議あり」を音で知らせるため

  function CookieDataSet(){ $this->__construct(); }

  function __construct(){
    $this->day_night  = $_COOKIE['day_night'];
    $this->vote_times = (int)$_COOKIE['vote_times'];
    $this->objection  = $_COOKIE['objection'];
  }
}

//-- 画像管理クラスの基底クラス --//
class ImageManager{
  function GenerateTag($name, $alt = ''){
    $str = '<img';
    if($this->class != '') $str .= ' class="' . $this->class . '"';
    $str .= ' src="' . JINRO_IMG . '/' . $this->path . '/' . $name . '.' . $this->extention . '"';
    if($alt != ''){
      EscapeStrings(&$alt);
      $str .= ' alt="' . $alt . '" title="' . $alt . '"';
    }
    return $str . '>';
  }
}

//-- 役職の画像処理の基底クラス --//
class RoleImageBase extends ImageManager{
  function DisplayImage($name){
    echo $this->GenerateTag($name) . '<br>'."\n";
  }
}

//-- 勝利陣営の画像処理の基底クラス --//
class VictoryImageBase extends ImageManager{
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

//-- 音源処理の基底クラス --//
class SoundBase{
  //音を鳴らす
  function Output($type, $loop = false){
    $path = JINRO_ROOT . '/' . $this->path . '/' . $this->$type . '.' . $this->extention;
    if($loop) $loop_tag = "\n".'<param name="loop" value="true">';

    echo <<< EOF
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=4,0,0,0" width="0" height="0">
<param name="movie" value="{$path}">
<param name="quality" value="high">{$loop_tag}
<embed src="{$path}" type="application/x-shockwave-flash" quality="high" width="0" height="0" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
</embed>
</object>

EOF;
  }
}
?>
