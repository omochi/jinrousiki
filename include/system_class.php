<?php
//-- �ǡ����١��������ε��ꥯ�饹 --//
class DatabaseConfigBase{
  //�ǡ����١�����³
  /*
    $header : HTML�إå����Ͼ��� [true: ���ϺѤ� / false: ̤����]
    $exit   : ���顼���� [true: exit ���֤� / false �ǽ�λ]
  */
  function Connect($header = false, $exit = true){
    //�ǡ����١��������Ф˥�������
    $db_handle = mysql_connect($this->host, $this->user, $this->password);
    if($db_handle){ //������������
      mysql_set_charset('ujis');
      if(mysql_select_db($this->name, $db_handle)){ //�ǡ����١�����³
	//mysql_query("SET NAMES utf8");
	//����������ϥ�ɥ���֤��ƽ�����λ
	$this->db_handle = $db_handle;
	return $db_handle;
      }
      else{
	$error_title = '�ǡ����١�����³����';
	$error_name  = $this->name;
      }
    }
    else{
      $error_title = 'MySQL��������³����';
      $error_name  = $this->host;
    }

    $error_message = $error_title . ': ' . $error_name; //���顼��å���������
    if($header){
      echo '<font color="#FF0000">' . $error_message . '</font><br>';
      if($exit) OutputHTMLFooter($exit);
      return false;
    }
    OutputActionResult($error_title, $error_message);
  }

  //�ǡ����١����Ȥ���³���Ĥ���
  function Disconnect($unlock = false){
    if(is_null($this->db_handle)) return;

    if($unlock) mysql_query('UNLOCK TABLES'); //��å����
    mysql_close($this->db_handle);
    $this->db_handle = NULL; //�ϥ�ɥ�򥯥ꥢ
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
	OutputActionResult('���顼', '̵����¼�ֹ�Ǥ���' . $request->room_no);
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

//-- ���å����ǡ����Υ��ɽ��� --//
class CookieDataSet{
  var $day_night;  //�������򲻤Ǥ��餻�뤿��
  var $vote_times; //����ɼ�򲻤��Τ餻�뤿��
  var $objection;  //�ְ۵Ĥ���פ򲻤��Τ餻�뤿��

  function CookieDataSet(){ $this->__construct(); }

  function __construct(){
    $this->day_night  = $_COOKIE['day_night'];
    $this->vote_times = (int)$_COOKIE['vote_times'];
    $this->objection  = $_COOKIE['objection'];
  }
}

//-- �����������饹�δ��쥯�饹 --//
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

//-- �򿦤β��������δ��쥯�饹 --//
class RoleImageBase extends ImageManager{
  function DisplayImage($name){
    echo $this->GenerateTag($name) . '<br>'."\n";
  }
}

//-- �����رĤβ��������δ��쥯�饹 --//
class VictoryImageBase extends ImageManager{
  function MakeVictoryImage($victory_role){
    $name = $victory_role;
    switch($victory_role){
    case 'human':
      $alt = '¼�;���';
      break;

    case 'wolf':
      $alt = '��ϵ����';
      break;

    case 'fox1':
    case 'fox2':
      $name = 'fox';
      $alt = '�ŸѾ���';
      break;

    case 'lovers':
      $alt = '���;���';
      break;

    case 'quiz':
      $alt = '����Ծ���';
      break;

    case 'draw':
    case 'vanish':
    case 'quiz_dead':
      $name = 'draw';
      $alt = '����ʬ��';
      break;

    default:
      return '-';
      break;
    }
    return $this->GenerateTag($name, $alt);
  }
}

//-- ���������δ��쥯�饹 --//
class SoundBase{
  //�����Ĥ餹
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
