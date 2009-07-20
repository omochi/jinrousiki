<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');

//���å���󳫻�
session_start();
$session_id = session_id();

EncodePostData(); //�ݥ��Ȥ��줿ʸ��������ƥ��󥳡��ɤ���

//���������
$room_no     = (int)$_GET['room_no']; //���� No
$auto_reload = (int)$_GET['auto_reload']; //�����ȥ���ɤδֳ�
$dead_mode   = $_GET['dead_mode'];   //��˴�ԥ⡼��
$heaven_mode = $_GET['heaven_mode']; //���å⡼��
// $view_mode   = $_GET['view_mode'];   //����⡼��
$list_down  = $_GET['list_down']; //�ץ쥤�䡼�ꥹ�Ȥ򲼤ˤ���
$play_sound = $_GET['play_sound'];//���Ǥ��Τ餻
if($play_sound == 'on'){
  $cookie_day_night  = $_COOKIE['day_night'];       //�������򲻤Ǥ��餻�뤿��
  $cookie_vote_times = (int)$_COOKIE['vote_times']; //����ɼ�򲻤��Τ餻�뤿��
  $cookie_objection  = $_COOKIE['objection'];       //�ְ۵Ĥ���פ򲻤��Τ餻�뤿��
}

$say = $_POST['say']; //ȯ��
$font_type = $_POST['font_type']; //�ե���ȥ�����
$set_objection = $_POST['set_objection']; //�ְ۵ġפ��ꡢ�Υ��å�

$dbHandle = ConnectDatabase(); //DB ��³
$uname = CheckSession($session_id); //���å���� ID ������å�

//���դȥ���������
$sql = mysql_query("SELECT date, day_night, room_name, room_comment, game_option FROM room
			WHERE room_no = $room_no");
$array = mysql_fetch_assoc($sql);
$room_name    = $array['room_name'];
$room_comment = $array['room_comment'];
$game_option  = $array['game_option'];
$date         = $array['date'];
$day_night    = $array['day_night'];

//��ʬ�Υϥ�ɥ�͡��ࡢ��䡢��¸�����
$sql = mysql_query("SELECT user_no, handle_name, sex, role, live, last_load_day_night FROM user_entry
			WHERE room_no = $room_no AND uname = '$uname' AND user_no > 0");
$array = mysql_fetch_assoc($sql);
$user_no             = $array['user_no'];
$handle_name         = $array['handle_name'];
$sex                 = $array['sex'];
$role                = $array['role'];
$live                = $array['live'];
$last_load_day_night = $array['last_load_day_night'];

$system_time = TZTime(); //���߻�������
$sudden_death_time = 0; //������¹ԤޤǤλĤ����

//ɬ�פʥ��å����򥻥åȤ���
$objection_array = array(); //SendCookie();�ǳ�Ǽ����롦�۵Ĥ���ξ���
$objection_left_count = 0;  //SendCookie();�ǳ�Ǽ����롦�۵Ĥ���λĤ���
SendCookie();

// //���ԤΥ����å� //�ɤ�����⻲�Ȥ���Ƥʤ�����
// $sql = mysql_query("SELECT victory_role FROM room WHERE room_no = $room_no");
// $victory_flag = (mysql_result($sql, 0, 0) != NULL);

//ȯ����̵ͭ������å�
EscapeStrings(&$say, false); //���������׽���
if($say != '' && $live == 'live' && ($day_night == 'day' || $day_night == 'night')){ //ȯ���ִ���
  if(strpos($role, 'cute_wolf') !== false && mt_rand(1, 100) <= $GAME_CONF->cute_wolf_rate)
    $say = $MESSAGE->wolf_howl; //˨ϵ�����Ψ��ȯ�������ʤ��ˤʤ�
  elseif((strpos($role, 'gentleman') !== false || strpos($role, 'lady') !== false) &&
	 mt_rand(1, 100) <= $GAME_CONF->gentleman_rate){ //�»Ρ��ʽ���ȯ�����Ƥ��ִ�
    $role_name = (strpos($role, 'gentleman') !== false ? 'gentleman' : 'lady');
    $message_header = $role_name . '_header';
    $message_footer = $role_name . '_footer';

    $sql = mysql_query("SELECT handle_name FROM user_entry WHERE room_no = $room_no
			AND live = 'live' AND uname <> '$uname' AND user_no > 0");
    $count = mysql_num_rows($sql) - 1;
    $rand_key = mt_rand(0, $count);
    $say = $MESSAGE->$message_header . mysql_result($sql, $rand_key, 0) . $MESSAGE->$message_footer;
  }
  elseif(strpos($role, 'liar') !== false){ //ϵ��ǯ��ȯ�����Ƥ��ִ�
    if(mt_rand(1, 100) <= $GAME_CONF->liar_rate) $say = strtr($say, $GAME_CONF->liar_replace_list);
  }
  if(strpos($role, 'invisible') !== false){ //�����º̤ν���
    $new_say = '';
    $count = mb_strlen($say);
    for($i = 0; $i < $count; $i++){
      $this_str = mb_substr($say, $i, 1);
      if($this_str == "\n" || $this_str == "\t") continue; //���ԥ����ɡ����֤��оݳ�
      if(mt_rand(1, 100) <= $GAME_CONF->invisible_rate)
	$new_say .= (strlen($this_str) == 2 ? '��' : ' ');
      else
	$new_say .= $this_str;
    }
    $say = $new_say;
  }
  if(strpos($role, 'silent') !== false){ //̵���ν���
    if(mb_strlen($say) > $GAME_CONF->silent_length)
      $say = mb_substr($say, 0, $GAME_CONF->silent_length) . '�ġ�';
  }
}

if($say != '' && $font_type == 'last_words' && $live == 'live')
  EntryLastWords($say);  //�����Ƥ���а����Ͽ
elseif($say != '' && ($last_load_day_night == $day_night ||
		      $live == 'dead' || $uname == 'dummy_boy'))
  Say($say); //���Ǥ��뤫���Ǹ�˥���ɤ������ȥ����󤬰��פ��Ƥ��뤫�����귯�ʤ�񤭹���
else
  CheckSilence(); //���������ڤΥ����å�(���ۡ�������)

//�Ǹ�˥���ɤ������Υ�����򹹿�
mysql_query("UPDATE user_entry SET last_load_day_night = '$day_night'
		WHERE room_no = $room_no AND uname = '$uname' AND user_no > 0");
mysql_query('COMMIT');

OutputGamePageHeader(); //HTML�إå�
OutputGameHeader(); //�����Υ����ȥ�ʤ�

if($heaven_mode != 'on'){
  if($list_down != 'on') OutputPlayerList(); //�ץ쥤�䡼�ꥹ��
  OutputAbility(); //��ʬ����������
  if($day_night == 'day' && $live == 'live') CheckSelfVote(); //��ɼ�Ѥߥ����å�
  OutputRevoteList(); //����ɼ�λ�����å�������ɽ������
}

//���å������
if($live == 'dead' && $heaven_mode == 'on')
  OutputHeavenTalkLog();
else
  OutputTalkLog();

if($heaven_mode != 'on'){
  if($live == 'dead') OutputAbilityAction(); //ǽ��ȯ��
  OutputLastWords(); //���
  OutputDeadMan();   //��˴��
  OutputVoteList();  //��ɼ���
  if($dead_mode != 'on') OutputSelfLastWords(); //��ʬ�ΰ��
  if($list_down == 'on') OutputPlayerList(); //�ץ쥤�䡼�ꥹ��
}
OutputHTMLFooter();

DisconnectDatabase($dbHandle); //DB ��³���

//-- �ؿ� --//
//ɬ�פʥ��å�����ޤȤ����Ͽ(�Ĥ��Ǥ˺ǿ��ΰ۵Ĥ���ξ��֤������������˳�Ǽ)
function SendCookie(){
  global $GAME_CONF, $system_time, $room_no, $date, $day_night, $user_no, $live, $uname,
    $set_objection, $objection_array, $objection_left_count;

  //<�������򲻤Ǥ��Τ餻��>
  //���å����˳�Ǽ (�������˲��Ǥ��Τ餻�ǻȤ���ͭ�����°����)
  setcookie('day_night', $day_night, $system_time + 3600);

  //<�ְ۵ġפ���򲻤Ǥ��Τ餻��>
  //���ޤǤ˼�ʬ���ְ۵ġפ���򤷤��������
  $sql = mysql_query("SELECT COUNT(message) FROM system_message WHERE room_no = $room_no
			AND type = 'OBJECTION' AND message = '$user_no'");

  //�����Ƥ���(�����ཪλ��ϻ�ԤǤ�OK)�ְ۵ġפ��ꡢ�Υ��å��׵᤬����Х��åȤ���(����������ξ��)
  if($live == 'live' && $day_night != 'night' && $set_objection == 'set' &&
     mysql_result($sql, 0, 0) < $GAME_CONF->objection){
    InsertSystemMessage($user_no, 'OBJECTION');
    InsertSystemTalk('OBJECTION', $system_time, '', '', $uname);
    mysql_query('COMMIT');
  }

  //�桼�������������ƿͿ�ʬ�Ρְ۵Ĥ���פΥ��å������ۤ���
  $sql = mysql_query("SELECT COUNT(uname) FROM user_entry WHERE room_no = $room_no AND user_no > 0");
  // //�����ꥻ�å� (0 ���ܤ��Ѥ��ͤ�����ʤ������ݾڤ���Ƥ�������פ��ʡ�)
  // $objection_array = array();
  // unset($objection_array[0]);
  $objection_array = array_fill(1, mysql_result($sql, 0, 0), 0); //index �� 1 ����

  //message:�۵Ĥ���򤷤��桼�� No �Ȥ��β�������
  $sql = mysql_query("SELECT message, COUNT(message) AS message_count FROM system_message
			WHERE room_no = $room_no AND type = 'OBJECTION' GROUP BY message");
  while(($array = mysql_fetch_assoc($sql)) !== false){
    $this_user_no = (int)$array['message'];
    $this_count   = (int)$array['message_count'];
    $objection_array[$this_user_no] = $this_count;
  }

  //���å����˳�Ǽ (ͭ�����°����)
  $str = array_shift($objection_array);
  foreach($objection_array as $value) $str .= ',' . $value; //����޶��ڤ�
  setcookie('objection', $str, $system_time + 3600);

  //�Ĥ�۵Ĥ���β��
  $objection_left_count = $GAME_CONF->objection - $objection_array[$user_no];

  //<����ɼ�򲻤Ǥ��Τ餻��>
  //����ɼ�β�������
  $sql = mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
			AND date = $date AND type = 'RE_VOTE' ORDER BY message DESC");
  if(mysql_num_rows($sql) != 0){
    $last_vote_times = (int)mysql_result($sql, 0, 0); //�����ܤκ���ɼ�ʤΤ�����
    setcookie('vote_times', $last_vote_times, $system_time + 3600); //���å����˳�Ǽ (ͭ�����°����)
  }
  else{
    setcookie('vote_times', '', $system_time - 3600); //���å��������� (ͭ�����°����)
  }
}

//�����Ͽ
function EntryLastWords($say){
  global $room_no, $day_night, $uname, $role, $live;

  //�����ཪλ�塢��ԡ��֥󲰡�ɮ�����ʤ���Ͽ���ʤ�
  if($day_night == 'aftergame' || $live != 'live' || strpos($role, 'reporter') !== false ||
     strpos($role, 'no_last_words') !== false) return false;

  //�����Ĥ�
  mysql_query("UPDATE user_entry SET last_words = '$say' WHERE room_no = $room_no
		AND uname = '$uname' AND user_no > 0");
  mysql_query('COMMIT'); //������ߥå�
}

//ȯ��
function Say($say){
  global $room_no, $game_option, $day_night, $uname, $role, $live;

  if(strpos($game_option, 'real_time') !== false){ //�ꥢ�륿������
    GetRealPassTime(&$left_time);
    $spend_time = 0; //���äǻ��ַв���������̵���ˤ���
  }
  else{ //���äǻ��ַв���
    GetTalkPassTime(&$left_time); //�в���֤���
    if(strlen($say) <= 100) //�в����
      $spend_time = 1;
    elseif(strlen($say) <= 200)
      $spend_time = 2;
    elseif(strlen($say) <= 300)
      $spend_time = 3;
    else
      $spend_time = 4;
  }

  if($day_night == 'beforegame' || $day_night == 'aftergame') //�����೫������Ϥ��Τޤ�ȯ��
    Write($say, $day_night, 0, true);
  elseif($uname == 'dummy_boy'){ //�����귯 (���� GM �б�)
    if($live == 'live' && $day_night == 'day' && $left_time > 0) //�����Ƥ������»��������
      Write($say, 'day', $spend_time, true); //�̾��̤�ȯ��
    else //����ʳ������ѤΥ����ƥ��å��������ڤ��ؤ�
      Write($say, "$day_night dummy_boy", 0, false); //ȯ�����֤򹹿����ʤ�
  }
  elseif($live == 'dead') //��˴�Ԥ�����
    Write($say, 'heaven', 0, false); //ȯ�����֤򹹿����ʤ�
  elseif($live == 'live' && $left_time > 0){ //��¸�Ԥ����»�����
    if($day_night == 'day') //��Ϥ��Τޤ�ȯ��
      Write($say, 'day', $spend_time, true);
    elseif($day_night == 'night'){ //��������ʬ����
      if(strpos($role, 'wolf') !== false) //ϵ
	Write($say, 'night wolf', $spend_time, true);
      elseif(strpos($role, 'common') !== false) //��ͭ��
	Write($say, 'night common', 0);
      elseif(strpos($role, 'fox') !== false && strpos($role, 'child_fox') === false) //�Ÿ�
	Write($say, 'night fox', 0);
      else //�Ȥ��
	Write($say, 'night self_talk', 0);
    }
  }
}

//ȯ���� DB ����Ͽ����
function Write($say, $location, $spend_time, $update = false){
  global $system_time, $room_no, $date, $day_night, $uname, $role, $live, $font_type;

  //�����礭�������
  if($live == 'live' && ($day_night == 'day' || $day_night == 'night')){
    if(    strpos($role, 'strong_voice') !== false) $voice = 'strong';
    elseif(strpos($role, 'normal_voice') !== false) $voice = 'normal';
    elseif(strpos($role, 'weak_voice')   !== false) $voice = 'weak';
    elseif(strpos($role, 'random_voice') !== false){
      $voice_list = array('strong', 'normal', 'weak');
      $rand_key = array_rand($voice_list);
      $voice = $voice_list[$rand_key];
    }
    else $voice = $font_type;
  }
  else $voice = $font_type;

  InsertTalk($room_no, $date, $location, $uname, $system_time, $say, $voice, $spend_time);
  if($update) UpdateTime();
  mysql_query('COMMIT'); //������ߥå�
}

//���������ڤΥ����å�
function CheckSilence(){
  global $TIME_CONF, $MESSAGE, $system_time, $sudden_death_time,
    $room_no, $game_option, $date, $day_night;

  //��������ʳ��Ͻ����򤷤ʤ�
  if($day_night != 'day' && $day_night != 'night') return false;

  //�ơ��֥��å�
  if(! mysql_query("LOCK TABLES room WRITE, talk WRITE, vote WRITE,
			user_entry WRITE, system_message WRITE")){
    return false;
  }

  //�Ǹ��ȯ�����줿���֤����
  $sql = mysql_query("SELECT last_updated FROM room WHERE room_no = $room_no");
  $last_updated_time = mysql_result($sql, 0, 0);
  $last_updated_pass_time = $system_time - $last_updated_time;

  //�в���֤����
  if(strpos($game_option, 'real_time') !== false) //�ꥢ�륿������
    GetRealPassTime(&$left_time);
  else //���äǻ��ַв���
    $silence_pass_time = GetTalkPassTime(&$left_time, true);

  //�ꥢ�륿�������Ǥʤ������»�������������ͤ�Ķ�����ʤ�ʤ����ֿʤ��(����)
  if(strpos($game_option, 'real_time') === false && $left_time > 0){
    if($last_updated_pass_time > $TIME_CONF->silence){
      $sentence = '�������������������� ' . $silence_pass_time . ' ' . $MESSAGE->silence;
      InsertTalk($room_no, $date, "$day_night system", 'system', $system_time,
		 $sentence, NULL, $TIME_CONF->silence_pass);
      UpdateTime();
    }
  }
  elseif($left_time == 0){ //���»��֤�᤮�Ƥ�����ٹ��Ф�
    //������ȯư�ޤǤλ��֤����
    if(strpos($game_option, 'quiz') !== false)
      $sudden_death_base_time = $TIME_CONF->sudden_death_quiz;
    else
      $sudden_death_base_time = $TIME_CONF->sudden_death;

    $left_time_str = ConvertTime($sudden_death_base_time); //ɽ���Ѥ��Ѵ�
    $sudden_death_announce = "����" . $left_time_str . "��" . $MESSAGE->sudden_death_announce;

    //���˷ٹ��Ф��Ƥ��뤫�����å�
    $sql = mysql_query("SELECT COUNT(uname) FROM talk WHERE room_no = $room_no
			AND date = $date AND location = '$day_night system'
			AND uname = 'system' AND sentence = '$sudden_death_announce'");
    if(mysql_result($sql, 0, 0) == 0){ //�ٹ��Ф��Ƥ��ʤ��ä���Ф�
      InsertSystemTalk($sudden_death_announce, ++$system_time); //�����äθ�˽Ф�褦��
      UpdateTime(); //�������֤򹹿�
      $last_updated_pass_time = 0;
    }
    $sudden_death_time = $sudden_death_base_time - $last_updated_pass_time;

    //���»��֤�᤮�Ƥ�����̤��ɼ�οͤ������व����
    if($sudden_death_time <= 0){
      //��ɼ���Ƥ��ʤ��ͤ�������뤿��δ��� SQL ʸ
      //(��ɼ�Ѥߤοͤ򺸷�礷�ơ�����ɼ�Ѥ�=NULL����ɼ���Ƥ��ʤ��פ����)
      $query = "SELECT user_entry.uname, user_entry.handle_name, user_entry.role
		FROM user_entry left join tmp_sd on user_entry.uname = tmp_sd.uname
		WHERE user_entry.room_no = $room_no AND user_entry.live = 'live'
		AND user_entry.user_no > 0 AND tmp_sd.uname is NULL";
      if($day_night == 'day'){
	//��ɼ��������
	$sql = mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
				AND date = $date AND type = 'VOTE_TIMES'");
	$vote_times = mysql_result($sql, 0, 0);

	//��ɼ�ѤߤοͤΥƥ�ݥ��ơ��֥�����
	mysql_query("CREATE TEMPORARY TABLE tmp_sd SELECT uname FROM vote
			WHERE room_no = $room_no AND date = $date
			AND situation = 'VOTE_KILL' AND vote_times = $vote_times");
	//��ɼ���Ƥ��ʤ��ͤ����
	$sql_novote = mysql_query($query);
      }
      elseif($day_night == 'night'){
	//��ɼ�ѤߤοͤΥƥ�ݥ��ơ��֥�����
	mysql_query("CREATE TEMPORARY TABLE tmp_sd SELECT uname FROM vote
			WHERE room_no = $room_no AND date = $date
			AND (situation = 'WOLF_EAT' OR situation = 'MAGE_DO'
			OR situation = 'GUARD_DO' OR situation = 'CUPID_DO' OR situation = 'MANIA_DO')");

	//��ɼ���Ƥ��ʤ��ͤ���� (�򿦤Τ�)
	$query .= " AND (user_entry.role LIKE '%wolf%' OR user_entry.role LIKE '%mage%'";
	if ($date == 1) {
	  $query .= " OR user_entry.role LIKE 'cupid%' OR user_entry.role LIKE 'mania%')";
	}
	else {
	  $query .= " OR user_entry.role LIKE 'guard%')";
	}
	$sql_novote = mysql_query($query);
      }

      //̤��ɼ�Ԥο�
      $novote_count = mysql_num_rows($sql_novote);

      //̤��ɼ�Ԥ����������व����
      for($i = 0; $i < $novote_count; $i++){
	$array = mysql_fetch_assoc($sql_novote);
	$this_uname  = $array['uname'];
	$this_handle = $array['handle_name'];
	$this_role   = $array['role'];

	SuddenDeath($this_uname, $this_handle, $this_role); //������¹�
      }
      InsertSystemTalk($MESSAGE->vote_reset, ++$system_time); //��ɼ�ꥻ�åȥ�å�����
      InsertSystemTalk($sudden_death_announce, ++$system_time); //��������Υ�å�����
      UpdateTime(); //���»��֥ꥻ�å�

      DeleteVote(); //��ɼ�ꥻ�å�
      CheckVictory(); //���ԥ����å�
    }
  }
  mysql_query('UNLOCK TABLES'); //�ơ��֥��å����
}

//¼̾�������ϡ������ܡ����פޤǡ����֤����(���Ԥ��Ĥ�����¼��̾�������ϡ����Ԥ����)
function OutputGameHeader(){
  global $GAME_CONF, $MESSAGE, $SOUND, $system_time, $sudden_death_time, $room_no,
    $room_name, $room_comment, $game_option, $dead_mode, $heaven_mode,
    $date, $day_night, $live, $handle_name, $auto_reload, $play_sound, $list_down,
    $cookie_day_night, $cookie_objection, $objection_array, $objection_left_count;

  $room_message = '<td class="room"><span>' . $room_name . '¼</span>����' . $room_comment .
    '��[' . $room_no . '����]</td>'."\n";
  $url_room   = '?room_no=' . $room_no;
  $url_reload = ($auto_reload > 0 ? '&auto_reload=' . $auto_reload : '');
  $url_sound  = ($play_sound  == 'on' ? '&play_sound=on'  : '');
  $url_list   = ($list_down   == 'on' ? '&list_down=on'   : '');
  $url_dead   = ($dead_mode   == 'on' ? '&dead_mode=on'   : '');
  $url_heaven = ($heaven_mode == 'on' ? '&heaven_mode=on' : '');
  $real_time  = (strpos($game_option, 'real_time') !== false);

  echo '<table class="game-header"><tr>'."\n";
  if(($live == 'dead' && $heaven_mode == 'on') || $day_night == 'aftergame'){ //��ȥ�������
    if($live == 'dead' && $heaven_mode == 'on')
      echo '<td>&lt;&lt;&lt;ͩ��δ�&gt;&gt;&gt;</td>'."\n";
    else
      echo $room_message;

    //�������Υ��ؤΥ������
    echo '<td class="view-option">��';

    $url_header ='<a href="game_log.php' . $url_room . '&log_mode=on&date=';
    $url_footer = '#game_top" target="_blank">';
    $url_day    = '&day_night=day'   . $url_footer;
    $url_night  = '&day_night=night' . $url_footer;

    echo $url_header . '1' . $url_night . '1(��)</a>'."\n";
    for($i=2; $i < $date; $i++){
      echo $url_header . $i . $url_day   . $i . '(��)</a>'."\n";
      echo $url_header . $i . $url_night . $i . '(��)</a>'."\n";
    }
    if($day_night == 'night' && $heaven_mode == 'on')
      echo $url_header . $date . $url_day . $date . '(��)</a>'."\n";
    elseif($day_night == 'aftergame'){
      $sql = mysql_query("SELECT COUNT(uname) FROM talk WHERE room_no = $room_no
				AND date = $date AND location = 'day'");
      if(mysql_num_rows($sql) > 0)
	echo $url_header . $date . $url_day . $date . '(��)</a>'."\n";
    }

    if($heaven_mode == 'on'){
      echo '</td>'."\n" . '</tr></table>'."\n";
      return;
    }
  }
  else{
    echo $room_message . '<td class="view-option">'."\n";
    if($live == 'dead' && $dead_mode == 'on'){ //��˴�Ԥξ��Ρ����������ɽ���Ͼ�⡼��
      $url = 'game_play.php' . $url_room . '&dead_mode=on' . $url_reload .
	$url_sound . $url_list . '#game_top';

      echo <<<EOF
<form method="POST" action="$url" name="reload_middle_frame" target="middle">
<input type="submit" value="����">
</form>

EOF;
    }
  }

  if($day_night != 'aftergame'){ //�����ཪλ��ϼ�ư�������ʤ�
    $url_header = '<a target="_top" href="game_frame.php' . $url_room .
      $url_dead . $url_heaven . $url_list;
    OutputAutoReloadLink($url_header . $url_sound  . '&auto_reload=');

    $url = $url_header . $url_reload . '&play_sound=';
    echo ' [���Ǥ��Τ餻](' .
      ($play_sound == 'on' ?  'on ' . $url . 'off">off</a>' : $url . 'on">on</a> off') .
      ')'."\n";
  }

  //�ץ쥤�䡼�ꥹ�Ȥ�ɽ������
  echo '<a target="_top" href="game_frame.php' . $url_room . $url_dead . $url_heaven .
    $url_reload . $url_sound  . '&list_down=' . ($list_down == 'on' ? 'off">��' : 'on">��') .
    '�ꥹ��</a>'."\n";

  //�������򲻤Ǥ��Τ餻����
  if($play_sound == 'on'){
    //�������ξ��
    if($cookie_day_night != $day_night && $day_night == 'day') OutputSound($SOUND->morning);

    //�۵Ĥ��ꡢ�򲻤��Τ餻��
    $cookie_objection_array = explode(',', $cookie_objection); //���å������ͤ�����˳�Ǽ����

    $count = count($objection_array);
    for($i = 1; $i <= $count; $i++){ //��ʬ��׻� (index �� 1 ����)
      //��ʬ����������̤��ǧ���Ʋ����Ĥ餹
      if((int)$objection_array[$i] > (int)$cookie_objection_array[$i]){
	$sql = mysql_query("SELECT sex FROM user_entry WHERE room_no = $room_no AND user_no = $i");
	$objection_sound = 'objection_' . mysql_result($sql, 0, 0);
	// OutputSound($SOUND->$objection_sound, false); //�롼�פ򤤤ä����ڤ�
      }
    }
  }
  echo '</td></tr>'."\n" . '</table>'."\n";

  switch($day_night){
    case 'beforegame': //����������դ����
      echo '<div class="caution">'."\n";
      echo '������򳫻Ϥ���ˤ������������೫�Ϥ���ɼ����ɬ�פ�����ޤ�';
      echo '<span>(��ɼ�����ͤ�¼�ͥꥹ�Ȥ��طʤ��֤��ʤ�ޤ�)</span>'."\n";
      echo '</div>'."\n";
      break;

    case 'day':
      $time_message = '�����פޤ� ';
      break;

    case 'night':
      $time_message = '���������ޤ� ';
      break;

    case 'aftergame': //���Է�̤���Ϥ��ƽ�����λ
      OutputVictory();
      return;
  }

  if($day_night == 'beforegame') OutputGameOption(); //�����४�ץ���������
  echo '<table class="time-table"><tr>'."\n";
  if($day_night != 'aftergame'){ //�����ཪλ��ʳ��ʤ顢�����ФȤλ��֥����ɽ��
    $date_str = gmdate('Y, m, j, G, i, s', $system_time);
    echo '<script type="text/javascript" src="javascript/output_diff_time.js"></script>'."\n";
    echo '<td>�����Фȥ�����PC�λ��֥���(�饰��)�� ' . '<span><script type="text/javascript">' .
      "output_diff_time('$date_str');" . '</script></span>' . '��</td></td>'."\n";
    echo '<tr>';
  }
  OutputTimeTable(); //�в���������¸�Ϳ������

  $left_time = 0;
  //�в���֤����
  if($real_time) //�ꥢ�륿������
    GetRealPassTime(&$left_time);
  else //���äǻ��ַв���
    $left_talk_time = GetTalkPassTime(&$left_time);

  if($day_night == 'beforegame' && $real_time){
    //�»��֤����»��֤����
    sscanf(strstr($game_option, 'time'), 'time:%d:%d', &$day_minutes, &$night_minutes);
    echo '<td class="real-time">';
    echo "������֡� �� <span>{$day_minutes}</span>ʬ / �� <span>{$night_minutes}</span>ʬ";
    echo '</td>';
  }
  if($day_night == 'day' || $day_night == 'night'){
    if($real_time){ //�ꥢ�륿������
      echo '<td class="real-time"><form name="realtime_form">'."\n";
      echo '<input type="text" name="output_realtime" size="50" readonly>'."\n";
      echo '</form></td>'."\n";
    }
    elseif($left_talk_time){ //ȯ���ˤ�벾�ۻ���
      echo '<td>' . $time_message . $left_talk_time . '</td>'."\n";
    }
  }

  //�۵Ĥ��ꡢ�Υܥ���(��Ȼ�ԥ⡼�ɰʳ�)
  if($day_night == 'beforegame' ||
     ($day_night == 'day' && $dead_mode != 'on' && $heaven_mode != 'on' && $left_time > 0)){
    $url = 'game_play.php' . $url_room . $url_reload . $url_sound . $url_list . '#game_top';
    echo <<<EOF
<td class="objection"><form method="POST" action="$url">
<input type="hidden" name="set_objection" value="set">
<input type="image" name="objimage" src="{$GAME_CONF->objection_image}" border="0">
</form></td>
<td>($objection_left_count)</td>

EOF;
  }
  echo '</tr></table>'."\n";

  if(($day_night == 'day' || $day_night == 'night') && $left_time == 0){
    echo '<div class="system-vote">' . $time_message . $MESSAGE->vote_announce . '</div>'."\n";
    if($sudden_death_time > 0)
      echo $MESSAGE->sudden_death_time . $sudden_death_time . '��<br>'."\n";
  }
}

//ŷ������å�����
function OutputHeavenTalkLog(){
  global $room_no, $game_option, $heaven_mode, $date, $day_night;

  //���Ͼ�������å�
  // global $uname, $live, $role;
  // if($live != 'dead') return false; //�ƤӽФ�¦�ǥ����å�����ΤǸ��ߤ�����

  //���äΥ桼��̾���ϥ�ɥ�̾��ȯ����ȯ���Υ����פ����
  $sql = mysql_query("SELECT user_entry.uname AS talk_uname,
			user_entry.handle_name AS talk_handle_name,
			user_entry.live AS talk_live,
			user_entry.sex AS talk_sex,
			user_icon.color AS talk_color,
			talk.sentence AS sentence,
			talk.font_type AS font_type,
			talk.location AS location
			FROM user_entry, talk, user_icon
			WHERE talk.room_no = $room_no
			AND talk.location LIKE 'heaven'
			AND ( (user_entry.room_no = $room_no AND user_entry.uname = talk.uname
			AND user_entry.icon_no = user_icon.icon_no)
			OR (user_entry.room_no = 0 AND talk.uname = 'system'
			AND user_entry.icon_no = user_icon.icon_no) )
			ORDER BY time DESC");

  echo '<table class="talk">'."\n";
  while(($array = mysql_fetch_assoc($sql)) !== false){
    $talk_uname  = $array['talk_uname'];
    $talk_handle = $array['talk_handle_name'];
    $talk_live   = $array['talk_live'];
    // $talk_sex    = $array['talk_sex'];  //����̤����
    $talk_color  = $array['talk_color'];
    $sentence    = $array['sentence'];
    $font_type   = $array['font_type'];
    // $location    = $array['location']; //����̤����

    LineToBR(&$sentence); //���Ԥ�<br>�������ִ�

    //����򿦤���������Ƥ�����Τ� HN ���ɲ�
    if(strpos($game_option, 'not_open_cast') === false)
      $talk_handle .= '<span>(' . $talk_uname . ')</span>';

    //���ý���
    echo '<tr class="user-talk">'."\n";
    echo '<td class="user-name"><font color="' . $talk_color . '">��</font>' .
      $talk_handle . '</td>'."\n";
    echo '<td class="say ' . $font_type . '">' . $sentence . '</td>'."\n";
    echo '</tr>'."\n";
  }
  echo '</table>'."\n";
}

//ǽ�Ϥμ���Ȥ������������
function OutputAbility(){
  global $ROLE_IMG, $MESSAGE, $room_no, $game_option, $date, $day_night,
    $user_no, $uname, $handle_name, $role, $live;

  //��������Τ�ɽ������
  if($day_night == 'beforegame' || $day_night == 'aftergame') return false;

  if($live == 'dead'){ //��˴������ǽ�Ϥ�ɽ�����ʤ�
    echo '<span class="ability-dead">' . $MESSAGE->ability_dead . '</span><br>';
    return;
  }

  if(strpos($role, 'human') !== false || strpos($role, 'suspect') !== false ||
     strpos($role, 'unconscious') !== false) OutputRoleComment('human');
  elseif(strpos($role, 'wolf') !== false){
    if(    strpos($role, 'boss_wolf')   !== false) OutputRoleComment('boss_wolf');
    elseif(strpos($role, 'poison_wolf') !== false) OutputRoleComment('poison_wolf');
    elseif(strpos($role, 'tongue_wolf') !== false) OutputRoleComment('tongue_wolf');
    elseif(strpos($role, 'cute_wolf')   !== false) OutputRoleComment('cute_wolf');
    else OutputRoleComment('wolf');

    //��֤�ɽ��
    OutputPartner("role LIKE '%wolf%' AND uname <> '$uname'", 'wolf_partner');

    //�����̵�ռ���ɽ��
    if($day_night == 'night') OutputPartner("role LIKE 'unconscious%'", 'unconscious_list');

    //���ϵ�γ��߷�̤�ɽ��
    $action = 'WOLF_EAT';
    $active_tongue_wolf = (strpos($role, 'tongue_wolf') !== false &&
			   strpos($role, 'lost_ability') === false);
    if($active_tongue_wolf){
      $sql = GetAbilityActionResult($action);
      $count = mysql_num_rows($sql);
      for($i = 0; $i < $count; $i++){
	list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
	if($handle_name != $actor) continue; //��ʬ�γ��߷�̤Τ�ɽ��

	//������ͤ��򿦤����
	$sql_target = mysql_query("SELECT role FROM user_entry WHERE room_no = $room_no
					AND handle_name = '$target' AND user_no > 0");
	$target_role = GetMainRole(mysql_result($sql_target, 0, 0));
	if($target_role == 'human')
	  $result_role = 'lost_ability'; //¼�ͤʤ�ǽ�ϼ���
	else
	  $result_role = 'result_' . $target_role;
	OutputAbilityResult('wolf_result', $target, $result_role);
      }
    }

    if($day_night == 'night'){ //��γ�����ɼ
      CheckNightVote($action, 'wolf-eat');

      //���ϵ��ǽ�ϼ���Ƚ��
      if($active_tongue_wolf){ //�����ǽ�������Τ�̵�̤�¿���Τ� TONGUE_WOLF_RESULT ����٤�
	$sql = GetAbilityActionResult($action);
	$count = mysql_num_rows($sql);
	for($i = 0; $i < $count; $i++){
	  list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
	  if($handle_name != $actor) continue; //��ʬ�γ��߷�̤Τ�ɽ��

	  //������ͤ��򿦤����
	  $sql_target = mysql_query("SELECT role FROM user_entry WHERE room_no = $room_no
					AND handle_name = '$target' AND user_no > 0");
	  $target_role = GetMainRole(mysql_result($sql_target, 0, 0));
	  if($target_role == 'human'){
	    $role .= ' lost_ability';
	    mysql_query("UPDATE user_entry SET role = '$role' WHERE room_no = $room_no
				AND uname = '$uname' AND user_no > 0");
	  }
	}
      }
    }
  }
  elseif(strpos($role, 'mage') !== false){
    if(strpos($role, 'soul_mage') !== false) OutputRoleComment('soul_mage');
    else OutputRoleComment('mage'); //̴���� (dummy_mage) ��ޤ�

    //�ꤤ��̤�ɽ��
    $action = 'MAGE_RESULT';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($handle_name == $actor) //��ʬ���ꤤ��̤Τ�ɽ��
	OutputAbilityResult('mage_result', $target, 'result_' . $target_role);
    }

    if($day_night == 'night') CheckNightVote('MAGE_DO', 'mage-do'); //����ꤤ��ɼ
  }
  elseif(strpos($role, 'necromancer') !== false || strpos($role, 'medium') !== false){
    if(strpos($role, 'necromancer') !== false){
      $role_name = 'necromancer';
      $action    = 'NECROMANCER_RESULT';
      $result    = 'necromancer_result';
    }
    else{
      $role_name = 'medium';
      $action    = 'MEDIUM_RESULT';
      $result    = 'medium_result';
    }
    OutputRoleComment($role_name);

    //Ƚ���̤�ɽ��
    $sql = GetAbilityActionResult($action);
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($target, $target_role) = ParseStrings(mysql_result($sql, $i, 0));
      OutputAbilityResult($result, $target, 'result_' . $target_role);
    }
  }
  elseif(strpos($role, 'fanatic_mad') !== false){
    OutputRoleComment('fanatic_mad');

    //ϵ��ɽ�� //��֤���ʤ��������Ѥβ�������٤�
    OutputPartner("role LIKE '%wolf%'", 'wolf_partner');
  }
  elseif(strpos($role, 'mad') !== false) OutputRoleComment('mad');
  elseif(strpos($role, 'guard') !== false){
    if(strpos($role, 'poison_guard') !== false) OutputRoleComment('poison_guard');
    else OutputRoleComment('guard');

    //��ҷ�̤�ɽ��
    $sql = GetAbilityActionResult('GUARD_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($handle_name == $actor) OutputAbilityResult(NULL, $target, 'guard_success');
    }

    if($day_night == 'night' && $date != 1) CheckNightVote('GUARD_DO', 'guard-do'); //��θ����ɼ
  }
  elseif(strpos($role, 'reporter') !== false){
    OutputRoleComment('reporter');

    //���Է�̤�ɽ��
    $action = 'REPORTER_SUCCESS';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){
      list($actor, $target, $wolf_handle) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($handle_name != $actor) continue; //��ʬ�����Է�̤Τ�ɽ��
      $target .= ' ����� ' . $wolf_handle;
      OutputAbilityResult('reporter_result_header', $target, 'reporter_result_footer');
    }

    if($day_night == 'night' && $date != 1) CheckNightVote('REPORTER_DO', 'guard-do'); //���������ɼ
  }
  elseif(strpos($role, 'common') !== false){
    OutputRoleComment('common');

    //��֤�ɽ��
    OutputPartner("role LIKE 'common%' AND uname <> '$uname'", 'common_partner');
  }
  elseif(strpos($role, 'child_fox') !== false){
    // OutputRoleComment('child_fox');
    echo '[���]<br>�����ʤ��ϡֻҸѡפǤ�������Ƥ��ˤޤ��󤬡���ϵ�˽�����Ȼ��Ǥ��ޤ��ޤ���<br>'."\n";

    //��֤�ɽ��
    OutputPartner("role LIKE '%fox%' AND uname <> '$uname'", 'fox_partner');
  }
  elseif(strpos($role, 'fox') !== false){
    OutputRoleComment('fox');

    //�ҸѰʳ�����֤�ɽ��
    OutputPartner("role LIKE 'fox%' AND uname <> '$uname'", 'fox_partner');

    //�Ѥ�����줿��å�������ɽ��
    $sql = GetAbilityActionResult('FOX_EAT');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){ //��ʬ������줿���Τ�ɽ��
      if($handle_name == mysql_result($sql, $i, 0)) OutputAbilityResult('fox_target', NULL);
    }
  }
  elseif(strpos($role, 'poison_cat') !== false){
    // OutputRoleComment('poison_cat');
    echo '[���]<br>�����ʤ��ϡ�ǭ���ס��Ǥ��äƤ��ޤ����ޤ��������ͤ�ï������ɤ餻������Ǥ��ޤ���<br>'."\n";

    //������̤�ɽ��
    $action = 'POISON_CAT_RESULT';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){ //��ʬ�η�̤Τ�ɽ��
      list($actor, $target, $result) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($handle_name == $actor) OutputAbilityResult(NULL, $target, 'poison_cat_' . $result);
    }

    //���������ɼ
    if($day_night == 'night' && $date != 1) CheckNightVote('POISON_CAT_DO', 'mania-do');
  }
  elseif(strpos($role, 'poison') !== false) OutputRoleComment('poison');
  elseif(strpos($role, 'pharmacist') !== false){
    // OutputRoleComment('pharmacist');
    echo '[���]<br>�����ʤ��ϡ����աס��跺�оݼԤ���ɼ���Ƥ������˸¤ꤽ�οͤ�̵�ǲ������뤳�Ȥ��Ǥ��ޤ���<br>'."\n";

    //���Ƿ�̤�ɽ��
    $sql = GetAbilityActionResult('PHARMACIST_SUCCESS');
    $count = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){ //��ʬ�β��Ƿ�̤Τ�ɽ������
      list($actor, $target) = ParseStrings(mysql_result($sql, $i, 0));
      if($handle_name == $actor) OutputAbilityResult(NULL, $target, 'pharmacist_success');
    }
  }
  elseif(strpos($role, 'cupid') !== false){
    OutputRoleComment('cupid');

    //��ʬ������Ǥä����� (��ʬ���ȴޤ�) ��ɽ������
    $cupid_id = strval($user_no);
    OutputPartner("role LIKE '%lovers[$cupid_id]%'", 'cupid_pair');

    if($day_night == 'night' && $date == 1) CheckNightVote('CUPID_DO', 'cupid-do'); //���������ɼ
  }
  elseif(strpos($role, 'mania') !== false){
    // OutputRoleComment('mania');
    echo '[���]<br>�����ʤ��ϡֿ��åޥ˥��פǤ���1���ܤ���˻��ꤷ���ͤΥᥤ���򿦤򥳥ԡ����뤳�Ȥ��Ǥ��ޤ� (���ͤ��ѹ�������ǽ��������ޤ�)<br>'."\n";

    if($day_night == 'night') CheckNightVote('MANIA_DO', 'mania-do'); //��Υ��ԡ���ɼ
  }
  elseif(strpos($role, 'quiz') !== false){
    OutputRoleComment('quiz');
    if(strpos($game_option, 'chaos') !== false){
      // OutputRoleComment('quiz_chaos');
      echo '����⡼�ɤǤϤ��ʤ��κ����ǽ�ϤǤ������̵��������ޤ���<br>'."\n";
      echo '�Ϥä�����ä�̵�������ʤΤǹ�������˥������Ǥ�Ф���ͷ�֤��ɤ��Ǥ��礦��<br>'."\n";
    }
  }

  //���������Ǥ��
  if(strpos($role, 'lovers') !== false){
    //���ͤ�ɽ������
    $lovers_str = GetLoversConditionString($role);
    OutputPartner("$lovers_str AND uname <> '$uname'", 'lovers_header', 'lovers_footer');
  }

  if(strpos($role, 'copied') !== false) {
    //���ԡ���̤�ɽ��
    $action = 'MANIA_RESULT';
    $sql    = GetAbilityActionResult($action);
    $count  = mysql_num_rows($sql);
    for($i = 0; $i < $count; $i++){ //��ʬ�η�̤Τ�ɽ��
      list($actor, $target, $target_role) = ParseStrings(mysql_result($sql, $i, 0), $action);
      if($handle_name == $actor) OutputAbilityResult(NULL, $target, 'result_' . $target_role);
    }
  }

  //����ʹߤϥ�������������ץ����αƶ��������
  if(strpos($game_option, 'secret_sub_role') !== false) return;

  //��ɼ��
  if(    strpos($role, 'authority')    !== false) OutputRoleComment('authority');
  elseif(strpos($role, 'rebel')        !== false) OutputRoleComment('rebel');
  elseif(strpos($role, 'random_voter') !== false) OutputRoleComment('random_voter');
  elseif(strpos($role, 'watcher')      !== false) OutputRoleComment('watcher');
  elseif(strpos($role, 'decide')       !== false); //����ԡ����¿������Τ��ʤ�
  elseif(strpos($role, 'plague')       !== false);
  elseif(strpos($role, 'good_luck')    !== false); //�������Ա������Τ��ʤ�
  elseif(strpos($role, 'bad_luck')     !== false);
  elseif(strpos($role, 'upper_luck')   !== false) OutputRoleComment('upper_luck');
  elseif(strpos($role, 'downer_luck' ) !== false) OutputRoleComment('downer_luck');
  elseif(strpos($role, 'random_luck' ) !== false) OutputRoleComment('random_luck');
  elseif(strpos($role, 'star')         !== false) OutputRoleComment('star');
  elseif(strpos($role, 'disfavor')     !== false) OutputRoleComment('disfaver');

  //ȯ���Ѳ���
  if(    strpos($role, 'strong_voice')  !== false) OutputRoleComment('strong_voice');
  elseif(strpos($role, 'normal_voice')  !== false) OutputRoleComment('normal_voice');
  elseif(strpos($role, 'weak_voice')    !== false) OutputRoleComment('weak_voice');
  elseif(strpos($role, 'random_voice')  !== false) OutputRoleComment('random_voice');

  //ȯ��������
  if(strpos($role, 'no_last_words') !== false) OutputRoleComment('no_last_words');
  if(strpos($role, 'blinder')       !== false) OutputRoleComment('blinder');
  if(strpos($role, 'earplug')       !== false) OutputRoleComment('earplug');
  if(strpos($role, 'silent')        !== false) OutputRoleComment('silent');

  //ȯ���Ѵ���
  if(strpos($role, 'liar')      !== false) OutputRoleComment('liar');
  if(strpos($role, 'invisible') !== false) OutputRoleComment('invisible');
  if(strpos($role, 'gentleman') !== false) OutputRoleComment('gentleman');
  elseif(strpos($role, 'lady')  !== false) OutputRoleComment('lady');

  //��ɼ����å����
  if(    strpos($role, 'chicken')      !== false) OutputRoleComment('chicken');
  elseif(strpos($role, 'rabbit')       !== false) OutputRoleComment('rabbit');
  elseif(strpos($role, 'perverseness') !== false) OutputRoleComment('perverseness');
  elseif(strpos($role, 'flattery')     !== false) OutputRoleComment('flattery');
  elseif(strpos($role, 'impatience')   !== false) OutputRoleComment('impatience');
}

//��������ɽ������
function OutputRoleComment($role){
  global $ROLE_IMG;
  echo '<img src="' . $ROLE_IMG->$role . '"><br>'."\n";
}

//��֤�ɽ������
function OutputPartner($query, $header, $footer = NULL){
  global $ROLE_IMG, $room_no;

  $sql = mysql_query("SELECT handle_name FROM user_entry WHERE room_no = $room_no
			AND user_no > 0 AND " . $query);
  $count = mysql_num_rows($sql);
  if($count < 1) return false; //��֤����ʤ����ɽ�����ʤ�

  echo '<table class="ability-partner"><tr>'."\n";
  echo '<td><img src="' . $ROLE_IMG->$header . '"></td>'."\n";
  echo '<td>��';
  for($i = 0; $i < $count; $i++) echo mysql_result($sql, $i, 0) . '���󡡡�';
  echo '</td>'."\n";
  if($footer) echo '<td><img src="' . $ROLE_IMG->$footer . '"></td>'."\n";
  echo '</tr></table>'."\n";
}

//ǽ��ȯư��̤�ǡ����١������䤤��碌��
function GetAbilityActionResult($action){
  global $room_no, $date;

  $yesterday = $date - 1;
  return mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
			AND date = $yesterday AND type = '$action'");
}
//ǽ��ȯư��̤�ɽ������
function OutputAbilityResult($header, $target, $footer = NULL){
  global $ROLE_IMG;

  echo '<table class="ability-result"><tr>'."\n";
  if($header) echo '<td><img src="' . $ROLE_IMG->$header . '"></td>'."\n";
  if($target) echo '<td>' . $target . '</td>';
  if($footer) echo '<td><img src="' . $ROLE_IMG->$footer . '"></td>'."\n";
  echo '</tr></table>'."\n";
}

//��ʬ��̤��ɼ�����å�
function CheckSelfVote(){
  global $room_no, $date, $uname;

  //��ɼ��������(����ɼ�ʤ� $vote_times ��������)
  $sql = mysql_query("SELECT message FROM system_message WHERE room_no = $room_no
			AND type = 'VOTE_TIMES' AND date = $date");
  $vote_times = (int)mysql_result($sql, 0, 0);
  echo '<div class="self-vote">��ɼ ' . $vote_times . ' ���ܡ�';

  //��ɼ�Ѥߤ��ɤ���
  $sql = mysql_query("SELECT COUNT(uname) FROM vote WHERE room_no = $room_no
			AND uname = '$uname' AND date = $date AND vote_times = $vote_times
			AND situation = 'VOTE_KILL'");
  echo (mysql_result($sql, 0, 0) ? '��ɼ�Ѥ�' : '�ޤ���ɼ���Ƥ��ޤ���') . '</div>'."\n";
}

//���̤��ɼ�����å�
function CheckNightVote($action, $class){
  global $MESSAGE, $room_no, $uname;

  $query = "SELECT uname FROM vote WHERE room_no = $room_no "; //��ͭ������
  if($action != 'WOLF_EAT') $query .= "AND uname = '$uname' "; //��ϵ��ï�Ǥ� OK
  $sql = mysql_query($query . "AND situation = '$action'");

  if(mysql_num_rows($sql) != 0) return false; //��ɼ�Ѥߤʤ��å�������ɽ�����ʤ�
  $class_str   = 'ability-' . $class; //���饹̾�ϥ��������������Ȥ�ʤ��Ǥ���
  $message_str = 'ability_' . strtolower($action);
  echo '<span class="' . $class_str . '">' . $MESSAGE->$message_str . '</span><br>'."\n";
}

//��ʬ�ΰ�������
function OutputSelfLastWords(){
  global $room_no, $day_night, $uname;

  //�����ཪλ���ɽ�����ʤ�
  if($day_night == 'aftergame') return false;

  $sql = mysql_query("SELECT last_words FROM user_entry WHERE room_no = $room_no
			AND uname = '$uname' AND user_no > 0");

  //�ޤ����Ϥ��Ƥʤ����ɽ�����ʤ�
  if(mysql_num_rows($sql) == 0) return false;

  $last_words = mysql_result($sql, 0, 0);
  LineToBR(&$last_words); //���ԥ����ɤ��Ѵ�
  if($last_words == '') return false;

  echo <<<EOF
<table class="lastwords" cellspacing="5"><tr>
<td class="lastwords-title">��ʬ�ΰ��</td>
<td class="lastwords-body">{$last_words}</td>
</tr></table>

EOF;
}
?>
