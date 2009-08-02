<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');
require_once(dirname(__FILE__) . '/include/request_class.php');

//����No����
$RQ_ARGS = new LogView();
$room_no  = $_GET['room_no'];
$log_mode = $_GET['log_mode'];
//$page        = (int)$_GET['page'];

$dbHandle = ConnectDatabase(); //DB ��³
if($RQ_ARGS->is_room){
  $USERS = new Users($room_no);
  OutputOldLog($RQ_ARGS->room_no);
}
else{
  OutputFinishedRooms($RQ_ARGS->page, $RQ_ARGS->reverse);
}
DisconnectDatabase($dbHandle); //DB ��³���

OutputHTMLFooter();

// �ؿ� //
//��������ɽ��
function OutputFinishedRooms($page, $reverse = NULL){
  global $SERVER_CONF, $MESSAGE, $ROOM_IMG, $VICTORY_IMG, $RQ_ARGS;

  //¼���γ�ǧ
  $sql = mysql_query("SELECT COUNT(*) FROM room WHERE status = 'finished'");
  $num_rooms = mysql_result($sql, 0);
  if($num_rooms == 0){
    OutputActionResult($SERVER_CONF->title . ' [����]', '���Ϥ���ޤ���<br>' . "\n" .
		       '<a href="index.php">�����</a>'."\n");
  }

  OutputHTMLHeader($SERVER_CONF->title . ' [����]', 'old_log_list');
echo <<<EOF
<body id="room_list">
<p><a href="index.php">�����</a></p>
<img src="img/old_log_title.jpg"><br>
<div align="center">
<table><tr><td class="list">
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
  if($RQ_ARGS->add_role == 'on') $url_option .= '&add_role=on';
  for($page_number = 1; $page_number <= $num_pages; $page_number++){
    $page_title = $page_number == $num_pages ? 'all' : $page_number;
    if($page != $page_title)
      echo " <a href=\"old_log.php?page=$page_title$url_option\">[$page_title]</a> ";
    else
      echo " [$page_title] ";
  }
  $reverse_text = ($is_reverse xor $config->reverse) ? '�����᤹' : '�����ؤ���';
  $base_url = 'old_log.php?'.($RQ_ARGS->add_role == 'on' ? '&add_role=on' : '').'reverse=';
  if($is_reverse)
    echo 'ɽ����:������ <a href="{$base_url}off">'.$reverse_text.'</a>';
  else
    echo 'ɽ����:�Ţ��� <a href="{$base_url}on">'.$reverse_text.'</a>';

  $game_option_list = array('dummy_boy', 'open_vote', 'not_open_cast', 'decide',
			    'authority', 'poison', 'cupid', 'boss_wolf', 'poison_wolf',
			    'mania', 'medium', 'liar', 'gentleman', 'sudden_death',
			    'chaos', 'chaos_open_cast', 'secret_sub_role', 'no_sub_role');

  echo <<<EOF
</td></tr>
<!--¼���� ��������-->
<tr><td>
<table class="main">
<tr><th>¼No</th><th>¼̾</th><th>¼�ˤĤ���</th><th>�Ϳ�</th><th>����</th><th>��</th></tr>

EOF;

  //����ɽ���ξ�硢��ڡ���������ɽ�����롣����ʳ������ꤷ��������ɽ��
  if($page == 'all')
    $limit_statement = '';
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

  while(($array = mysql_fetch_assoc($sql)) !== false){
    extract($array, EXTR_PREFIX_ALL, 'log');

    //���ץ����Ⱦ��Ԥβ���
    $game_option_str = MakeGameOptionImage($log_room_game_option, $log_room_option_role);
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
      $voctory_role_str = $VICTORY_IMG->GenerateTag('quiz', '����Ծ���', 'winner');
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
    if($RQ_ARGS->add_role == 'on') $base_url .= '&add_role=on';

    /*
    if ($DEBUG_MODE){
      $debug_anchor = "<a href=\"$base_url&debug=on\" $dead_room_color >Ͽ</a>";
    }
    */

    echo <<<EOF
<tr class="list">
<td class="number" rowspan="2">$log_room_no</td>
<td class="title"><a href="$base_url" $dead_room_color>$log_room_name ¼</a></td>
<td class="upper comment">�� $log_room_comment ��</td>
<td class="upper">$user_count ($log_room_max_user)</td>
<td class="upper">$log_room_date</td>
<td class="side">$voctory_role_str</td>
</tr>
<tr class="lower list">
<td class="comment">(
<a href="$base_url&reverse_log=on" $dead_room_color>��</a>
<a href="$base_url&heaven_talk=on" $dead_room_color>��</a>
<a href="$base_url&reverse_log=on&heaven_talk=on" $dead_room_color>��&amp;��</a>
<a href="$base_url&heaven_only=on" $dead_room_color >��</a>
<a href="$base_url&reverse_log=on&heaven_only=on" $dead_room_color>��&amp;��</a>
$debug_anchor
)
</td>
<td colspan="4">$game_option_str</td>
</tr>

EOF;
      }
  echo <<<EOF
</table>
</td></tr>
</table>
</div>

EOF;
}


//���������No�Υ�����Ϥ���
function OutputOldLog($room_no){
  global $SERVER_CONF, $RQ_ARGS, $status, $day_night, $last_date, $live;

  $base_title = $SERVER_CONF->title . ' [����]';
  $url = "<br>\n<a href=\"old_log.php\">�����</a>\n";

  if($room_no == NULL) OutputActionResult($title, '¼����ꤷ�Ƥ���������' . $url);

  //���դȥ���������
  $sql = mysql_query("SELECT date, day_night, room_name, room_comment, status
			FROM room WHERE room_no = $room_no");
  $array = mysql_fetch_assoc($sql);
  static $last_date; $last_date = $array['date'];
  $day_night    = $array['day_night'];
  $room_name    = $array['room_name'];
  $room_comment = $array['room_comment'];
  $status       = $array['status'];

  if($status != 'finished' || $day_night != 'aftergame'){
    OutputActionResult($base_title, '�ޤ����������Υ��ϱ����Ǥ��ޤ���' . $url);
  }

  $live = 'dead'; //¾�δؿ��˱ƶ������٤�ɽ�����뤿��
  $title = '[' . $room_no . '����] ' . $room_name . ' - ' . $base_title;

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
  if($is_reverse){
    OutputDateTalkLog(0, 'beforegame', $is_reverse);
    for($i = 1; $i <= $last_date; $i++){
      OutputDateTalkLog($i, '', $is_reverse);
    }
    OutputVictory();
    OutputDateTalkLog($last_date, 'aftergame', $is_reverse);
  }
  else{
    OutputDateTalkLog($last_date, 'aftergame', $is_reverse);
    OutputVictory();
    for($i = $last_date; $i > 0; $i--){
      OutputDateTalkLog($i, '', $is_reverse);
    }
    OutputDateTalkLog(0, 'beforegame', $is_reverse);
  }
}

//��ΤߤΥ�ɽ�����ɽ�����ޤ���
function LayoutHeaven($last_date, $is_reverse){
  if($is_reverse){
    for($i = 1; $i <= $last_date; $i++){
      OutputDateTalkLog($i, 'heaven_only', $is_reverse);
    }
  }
  else{
    for($i = $last_date; $i > 0; $i--){
      OutputDateTalkLog($i, 'heaven_only', $is_reverse);
    }
  }
}

//��������դβ��å������
function OutputDateTalkLog($set_date, $set_location, $is_reverse){
  global $RQ_ARGS, $ROLES, $room_no, $status, $date, $day_night, $live;

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
    if($set_location == 'heaven_only')
      $location_select = "AND ((talk.location = 'heaven') OR (talk.uname = 'system'))";
    else
      $location_select = "AND talk.location <> 'aftergame' AND talk.location <> 'beforegame'";
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
    $hide_heaven_query = ($RQ_ARGS->heaven_talk == 'on') ? '' : "AND talk.location <> 'heaven'";
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
  $builder = DocumentBuilder::Generate();
  $builder->BeginTalk("old-log-talk {$table_class}");
  while(($array = mysql_fetch_assoc($sql)) !== false){
    $location = $array['location'];
    if(strpos($location, 'day') !== false && $day_night != 'day'){
      $builder->EndTalk();
      OutputSceneChange($set_date);
      $day_night = 'day';
      echo '<table class="old-log-talk ' . $day_night . '">'."\n";
    }
    elseif(strpos($location, 'night') !== false && $day_night != 'night'){
      $builder->EndTalk();
      OutputSceneChange($set_date);
      $day_night = 'night';
      echo '<table class="old-log-talk ' . $day_night . '">'."\n";
    }
    OutputTalk($array, &$builder); //���ý���
  }
  $builder->EndTalk();

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
  global $RQ_ARGS, $date, $day_night;

  if($RQ_ARGS->heaven_only == 'on') return;
  $date = $set_date;
  if($RQ_ARGS->reverse_log == 'on'){
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