<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');
require_once(dirname(__FILE__) . '/include/request_class.php');

//����No����
$RQ_ARGS = new LogView();
$room_no     = $_GET['room_no'];
$log_mode    = $_GET['log_mode'];
$reverse_log = $_GET['reverse_log'];
$heaven_talk = $_GET['heaven_talk'];
$heaven_only = $_GET['heaven_only'];
$add_role    = $_GET['add_role'];
//$page        = (int)$_GET['page'];

$dbHandle = ConnectDatabase(); //DB ��³

if($RQ_ARGS->is_room)
  OutputOldLog($RQ_ARGS->room_no);
else
  OutputFinishedRooms($RQ_ARGS->page, $RQ_ARGS->reverse);

DisconnectDatabase($dbHandle); //DB ��³���

OutputHTMLFooter();

// �ؿ� //
//��������ɽ��
function OutputFinishedRooms($page, $reverse = NULL){
  global $MESSAGE, $ROOM_IMG, $VICTORY_IMG, $add_role;

  //¼���γ�ǧ
  $sql = mysql_query("SELECT COUNT(*) FROM room WHERE status = 'finished'");
  $num_rooms = mysql_result($sql, 0);
  if($num_rooms == 0){
    OutputActionResult('��Ͽ�ϵ�ʤ�䡩[����]', '���Ϥ���ޤ���<br>' . "\n" .
		       '<a href="index.php">�����</a>'."\n");
  }

  OutputHTMLHeader('��Ͽ�ϵ�ʤ�䡩[��������]', 'old_log_list');
echo <<<EOF
<body id="room_list">
<p><a href="index.php">�����</a></p>
<img src="img/old_log_title.jpg"><br>
<div align="center">
<table border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="right">
[�ڡ���]

EOF;

  $config = new OldLogConfig(); //��������
  if(empty($reverse))
    $is_reverse = $config->reverse;
  else
    $is_reverse = $reverse == 'on';

  //�ڡ�����󥯤ν���
  if($page == NULL) $page = 1;
  $num_pages = ceil($num_rooms / $config->one_page) + 1; //[all] �ΰ٤� + 1 ���Ƥ���
  $url_option = '&reverse='.($is_reverse ? 'on' : 'off');
  if($add_role == 'on') $url_option .= '&add_role=on';
  for($page_number = 1; $page_number <= $num_pages; $page_number++){
    $page_title = $page_number == $num_pages ? 'all' : $page_number;
    if($page != $page_title)
      echo " <a href=\"old_log.php?page=$page_title$url_option\">[$page_title]</a> ";
    else
      echo " [$page_title] ";
  }
  $reverse_text = ($is_reverse xor $config->reverse) ? '�����᤹' : '�����ؤ���';
  $base_url = 'old_log.php?'.($add_role == 'on' ? '&add_role=on' : '').'reverse=';
  if($is_reverse)
    echo 'ɽ����:������ <a href="{$base_url}off">'.$reverse_text.'</a>';
  else
    echo 'ɽ����:�Ţ��� <a href="{$base_url}on">'.$reverse_text.'</a>';

    $game_option_list = array('dummy_boy', 'open_vote', 'not_open_cast',
			      'decide', 'authority', 'poison', 'cupid', 'boss_wolf',
			      'poison_wolf', 'mania', 'medium', 'liar', 'gentleman', 'sudden_death',
			      'chaos', 'chaos_open_cast', 'secret_sub_role', 'no_sub_role');

    $option_column = count($game_option_list) + 2;
  echo <<<EOF
</td></tr>
<!--¼���� ��������-->
<tr><td>
<table class="main">
<tr class="column">
<th>¼No</th><th>¼̾</th><th>¼�ˤĤ���</th><th>�Ϳ�</th><th>��</th><th colspan="{$option_column}">���ץ����</th>
</tr>

EOF;

  //����ɽ���ξ�硢��ڡ���������ɽ�����롣����ʳ������ꤷ��������ɽ��
  if($page == 'all'){
    $limit_statement = '';
  }
  else{
    $start_number = $config->one_page * ($page - 1);
    $limit_statement = sprintf('LIMIT %d, %d', $start_number, $config->one_page);
  }

  //ɽ������Ԥμ���
  $room_order = ($is_reverse ? 'DESC' : '');
  $sql = mysql_query("SELECT room_no, room_name, room_comment, date AS room_date,
			game_option AS room_game_option, option_role AS room_option_role,
			max_user AS room_max_user, (SELECT COUNT(*) FROM user_entry user
			WHERE user.room_no = room.room_no AND user.user_no > 0)
			AS room_num_user, victory_role AS room_victory_role FROM room
			WHERE status = 'finished' ORDER BY room_no $room_order $limit_statement");

  while (($array = mysql_fetch_assoc($sql)) !== false){
    extract($array, EXTR_PREFIX_ALL, 'log');
    $log_room_game_option .= ' ' . $log_room_option_role;

    //���ץ����Ⱦ��Ԥβ���
    if(strpos($log_room_game_option,'wish_role') !== false)
      $log_wish_role_str = $ROOM_IMG->GenerateTag('wish_role', $MESSAGE->wish_role);
    else
      $log_wish_role_str = "<br>";

    if(strpos($log_room_game_option, 'real_time') !== false){
      if(strpos($log_room_game_option, 'real_time:') !== false){
        //�»��֤����»��֤����
        $real_time_str = strstr($log_room_game_option, 'real_time');
        sscanf(&$real_time_str, "real_time:%d:%d", &$day_minutes, &$night_minutes);
        $real_time_alt_str = $MESSAGE->game_option_real_time . "���롧 $day_minutes ʬ" .
	  "���롧 $night_minutes ʬ";
      }
      else $real_time_alt_str = $MESSAGE->game_option_real_time;
      $log_real_time_str = $ROOM_IMG->GenerateTag('real_time', $real_time_alt_str);
    }
    else
      $log_real_time_str = "<br>";

    switch($log_room_victory_role){
      case 'human':
        $voctory_role_str = $VICTORY_IMG->GenerateTag('human', '¼�;���', 'winner');
	break;
      case 'wolf':
        $voctory_role_str = $VICTORY_IMG->GenerateTag('wolf', '��ϵ����', 'winner');
	break;
	// case 'fox': //���ߤ� fox1 or fox2 �ΤߤʤΤ�����
      case 'fox1':
      case 'fox2':
        $voctory_role_str = $VICTORY_IMG->GenerateTag('fox', '�ŸѾ���', 'winner');
	break;
      case 'lovers':
        $voctory_role_str = $VICTORY_IMG->GenerateTag('lovers', '���;���', 'winner');
	break;
      case 'quiz':
        // $voctory_role_str = $VICTORY_IMG->GenerateTag('Qz', '���;���', 'winner');
        $voctory_role_str = 'Qz';
	break;
      case 'draw':
      case 'vanish':
      case 'quiz_dead':
        $voctory_role_str = $VICTORY_IMG->GenerateTag('draw', '����ʬ��', 'winner');
	break;
      default:
	$voctory_role_str = "-";
	break;
    }

    if($log_room_date == 0) //��¼�ξ�硢���򳥿��ˤ���
      $dead_room_color = ' style="color:silver"';
    else
      $dead_room_color = '';

    //�桼����������
    // $str_max_users = $ROOM_IMG->max_user_list[$log_room_max_user];
    $user_count = intval($log_room_num_user);

    $base_url = "old_log.php?log_mode=on&room_no=$log_room_no";
    if($add_role == 'on') $base_url .= '&add_role=on';

    /*
    if ($DEBUG_MODE){
      $debug_anchor = "<a href=\"$base_url&debug=on\" $dead_room_color >Ͽ</a>";
    }
     */

    echo <<<ROOM_ROW
<tr>
<td class="text">$log_room_no</td>
<td class="text">
  <a href="$base_url" $dead_room_color >$log_room_name ¼</a><br>
<span class="room-link">(<a href="$base_url&reverse_log=on" $dead_room_color>��</a>
<a href="$base_url&heaven_talk=on" $dead_room_color>��</a>
<a href="$base_url&reverse_log=on&heaven_talk=on" $dead_room_color>��&amp;��</a>
<a href="$base_url&heaven_only=on" $dead_room_color >��</a>
<a href="$base_url&reverse_log=on&heaven_only=on" $dead_room_color>��&amp;��</a>
$debug_anchor)
</span>
</td>
<td align="right" valign="middle"><small>�� $log_room_comment ��</small></td>
<td align="center" valign="middle">$user_count</td>
<td align="center" valign="middle">$voctory_role_str</td>
<td align="center" valign="middle">$log_wish_role_str</td>
<td align="center" valign="middle">$log_real_time_str</td>

ROOM_ROW;
    $wide_pict_list = array('boss_wolf', 'poison_wolf', 'secret_sub_role', 'no_sub_role');
    foreach($game_option_list as $this_option){
      if($this_option == 'chaos'){
	if(strpos($log_room_game_option, 'chaosfull') !== false){
	  $this_flag = true;
	  $this_option = 'chaosfull';
	}
	elseif(strpos($log_room_game_option, 'chaos') !== false)
	  $this_flag = true;
	else
	  $this_flag = false;
      }
      else
	$this_flag = (strpos($log_room_game_option, $this_option) !== false);

      if($this_flag){
	$message_str = 'game_option_' . $this_option;
	$this_str = $ROOM_IMG->GenerateTag($this_option, $MESSAGE->$message_str);
      }
      else
	$this_str = "<br>";
      echo '<td valign="middle">' . $this_str . '</td>'."\n";
    }
    echo '</tr>'."\n";
  }

  echo <<<FOOTER
</table>
</td></tr>
</table>
</div>

FOOTER;
}


//���������No�Υ�����Ϥ���
function OutputOldLog($room_no){
  // global $reverse_log, $heaven_only, $status, $day_night, $last_date, $live;
  global $RQ_ARGS, $status, $day_night, $last_date, $live;

  $base_title = '��Ͽ�ϵ�ʤ�䡩[����]';
  $url   = '<br>' . "\n" . '<a href="old_log.php">�����</a>'."\n";

  if($room_no == NULL) OutputActionResult($title, '¼����ꤷ�Ƥ���������' . $url);

  //���դȥ���������
  $sql = mysql_query("SELECT date, day_night, room_name, room_comment, status
			FROM room WHERE room_no = $room_no");
  $array = mysql_fetch_assoc($sql);
  static $last_date; $last_date   = $array['date'];
  $day_night    = $array['day_night'];
  $room_name    = $array['room_name'];
  $room_comment = $array['room_comment'];
  $status       = $array['status'];

  if($status != 'finished' || $day_night != 'aftergame'){
    OutputActionResult($base_title, '�ޤ����������Υ��ϱ����Ǥ��ޤ���' . $url);
  }

  $live = 'dead'; //¾�δؿ��˱ƶ������٤�ɽ�����뤿��
  $title = '[' . $room_no . '����]' . $room_name . ' - ' . $base_title;

  //���������Υڡ����ˤ���
  $referer_page_str = strstr($_SERVER['HTTP_REFERER'], 'page');
  sscanf($referer_page_str, "page=%s", &$referer_page);

  OutputHTMLHeader($title, 'old_log');
  echo <<<EOF
<a href="old_log.php?page=$referer_page">�����</a><br>
<div class="room"><span>{$room_name}¼</span>����{$room_comment}�� [{$room_no}����]</td></div>

EOF;
  OutputPlayerList();   //�ץ쥤�䡼�ꥹ�Ȥ����

  $layout = 'Layout'.($RQ_ARGS->heaven_only == 'on' ? 'Heaven' : 'TalkLog');
  $layout($last_date, $RQ_ARGS->reverse_log == 'on');
}

//�̾�Υ�ɽ�����ɽ�����ޤ���
function LayoutTalkLog($last_date, $is_reverse){
  if ($is_reverse) {
    OutputDateTalkLog(0, 'beforegame', $is_reverse);
    for($i = 1; $i <= $last_date; $i++) {
      OutputDateTalkLog($i, '', $is_reverse);
    }
    OutputVictory();
    OutputDateTalkLog($last_date, 'aftergame', $is_reverse);
  }
  else {
    OutputDateTalkLog($last_date, 'aftergame', $is_reverse);
    OutputVictory();
    for($i = $last_date; $i > 0; $i--) {
      OutputDateTalkLog($i, '', $is_reverse);
    }
    OutputDateTalkLog(0, 'beforegame', $is_reverse);
  }
}

//��ΤߤΥ�ɽ�����ɽ�����ޤ���
function LayoutHeaven($last_date, $is_reverse){
  if($is_reverse) {
    for($i = $last_date; $i > 0; $i--) {
      OutputDateTalkLog($i, 'heaven_only', $is_reverse);
    }
  }
  else {
    for($i = 1; $i <= $last_date; $i++) {
      OutputDateTalkLog($i, 'heaven_only', $is_reverse);
    }
  }
}

//��������դβ��å������
function OutputDateTalkLog($set_date, $set_location, $is_reverse){
  global $RQ_ARGS, $room_no, $status, $date, $day_night, $live;

  if($is_reverse) //�ս硢��������ǽ����ޤ�
    $select_order = 'ORDER BY time';
  else //�ǽ�����������ޤ�
    $select_order = 'ORDER BY time DESC';

  switch($set_location){
  case 'beforegame':
  case 'aftergame':
    $table_class = $set_location;
    $date_select = '';
    $location_select = "AND talk.location LIKE '$set_location%'";
    break;
  default:
    //�����ܰʹߤ��뤫��Ϥޤ�
    $table_class = ($is_reverse == 'on' && $set_date != 1) ? 'day' : 'night';
    $date_select = "AND talk.date = $set_date";
    if ($set_location == 'heaven_only') {
      $location_select = "AND ((talk.location = 'heaven') OR (talk.uname = 'system'))";
    }
    else {
      $location_select = "AND talk.location <> 'aftergame' AND talk.location <> 'beforegame'";
    }
    break;
  }
  if($set_location == 'heaven_only'){
    //���äΥ桼��̾���ϥ�ɥ�̾��ȯ����ȯ���Υ����פ����
    $sql = mysql_query("SELECT user_entry.uname AS talk_uname,
			user_entry.handle_name AS talk_handle_name,
			user_entry.role AS talk_role,
			user_entry.sex AS talk_sex,
			user_icon.color AS talk_color,
			talk.sentence AS sentence,
			talk.font_type AS font_type,
			talk.location AS location
			FROM user_entry, talk, user_icon
			WHERE talk.room_no = $room_no
			AND ( (user_entry.room_no = $room_no AND user_entry.uname = talk.uname
			AND user_entry.icon_no = user_icon.icon_no)
			OR ( user_entry.room_no = 0 AND talk.uname = 'system'
			AND user_entry.icon_no = user_icon.icon_no) )
			AND talk.date = $set_date
			AND ( (talk.location = 'heaven') OR (talk.uname = 'system') )
			$select_order");
  }
  elseif($set_location == 'beforegame' || $set_location == 'aftergame'){
    //���äΥ桼��̾���ϥ�ɥ�̾��ȯ����ȯ���Υ����פ����
    $sql = mysql_query("SELECT user_entry.uname AS talk_uname,
			user_entry.handle_name AS talk_handle_name,
			user_entry.role AS talk_role,
			user_entry.sex AS talk_sex,
			user_icon.color AS talk_color,
			talk.sentence AS sentence,
			talk.font_type AS font_type,
			talk.location AS location
			FROM user_entry, talk, user_icon
			WHERE talk.room_no = $room_no
			AND talk.location like '$set_location%'
			AND ( (user_entry.room_no = $room_no AND user_entry.uname = talk.uname
			AND user_entry.icon_no = user_icon.icon_no)
			OR (user_entry.room_no = 0 AND talk.uname = 'system'
			AND user_entry.icon_no = user_icon.icon_no) )
			$select_order");
  }
  else{
    $hide_heaven_query = ($RQ_ARGS->heaven_talk == 'on') ? "" : "AND talk.location <> 'heaven'";
    //���äΥ桼��̾���ϥ�ɥ�̾��ȯ����ȯ���Υ����פ����
    $sql = mysql_query("SELECT
			room_users.uname AS talk_uname,
			room_users.handle_name AS talk_handle_name,
			room_users.role AS talk_role,
			room_users.sex AS talk_sex,
			room_users.color AS talk_color,
			talk.sentence AS sentence,
			talk.font_type AS font_type,
			talk.location AS location
			FROM talk,
			  (SELECT
			  users.uname,
			  users.handle_name,
			  users.role,
			  users.sex,
			  user_icon.color
			  FROM user_entry users LEFT JOIN user_icon USING (icon_no)
			  WHERE users.room_no IN ($room_no, 0)) room_users
			WHERE talk.room_no = $room_no
			AND room_users.uname = talk.uname
			AND talk.date = $set_date
			AND talk.location <> 'aftergame'
			AND talk.location <> 'beforegame'
			$hide_heaven_query
			$select_order");
  }

  if($set_location != 'beforegame' && $set_location != 'aftergame' &&
     $set_date != $last_date && ! $is_reverse && $RQ_ARGS->heaven_only != 'on'){
    $date = $set_date + 1;
    $day_night = 'day';
    OutputLastWords(); //��������
    OutputDeadMan();   //��˴�Ԥ����
  }
  $day_night = $table_class;

  //����
  echo '<table class="old-log-talk ' . $table_class . '">'."\n";
  while(($array = mysql_fetch_assoc($sql)) !== false){
    $location = $array['location'];
    if(strpos($location, 'day') !== false && $day_night != 'day'){
      OutputSceneChange($set_date);
      $day_night = 'day';
      echo '<table class="old-log-talk ' . $day_night . '">'."\n";
    }
    elseif(strpos($location, 'night') !== false && $day_night != 'night'){
      OutputSceneChange($set_date);
      $day_night = 'night';
      echo '<table class="old-log-talk ' . $day_night . '">'."\n";
    }
    OutputTalk($array); //���ý���
  }
  echo '</table>'."\n";

  if($set_location != 'beforegame' && $set_location != 'aftergame' &&
     $set_date != $last_date && $is_reverse && $RQ_ARGS->heaven_only != 'on'){
    $day_night = 'day';
    $date = $set_date + 1;
    OutputDeadMan();   //��˴�Ԥ����
    OutputLastWords(); //��������
  }
}

//�������ڤ��ؤ����Υ�����
function OutputSceneChange($set_date){
  global $RQ_ARGS, $reverse_log, $heaven_only, $date, $day_night;

  echo '</table>'."\n";
  if($RQ_ARGS->heaven_only == 'on') return;
  $date = $set_date;
  if($reverse_log == 'on'){
    $day_night = 'night';
    OutputVoteList(); //��ɼ��̽���
    OutputDeadMan();  //��˴�Ԥ����
  }
  else{
    OutputDeadMan();  //��˴�Ԥ����
    OutputVoteList(); //��ɼ��̽���
  }
}
?>
