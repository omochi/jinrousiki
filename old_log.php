<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('game_play_functions', 'user_class', 'talk_class');
$INIT_CONF->LoadClass('ROLES', 'ICON_CONF', 'ROOM_IMG');

//����No����
$RQ_ARGS =& new LogView();
$room_no = $RQ_ARGS->room_no;

$dbHandle = ConnectDatabase(); //DB ��³
if($RQ_ARGS->is_room){
  $USERS =& new UserDataSet($RQ_ARGS);
  $SELF  =& new User();
  OutputOldLog();
}
else{
  OutputFinishedRooms($RQ_ARGS->page, $RQ_ARGS->reverse);
}
DisconnectDatabase($dbHandle); //DB ��³���

OutputHTMLFooter();

// �ؿ� //
//��������ɽ��
function OutputFinishedRooms($page, $reverse = NULL){
  global $SERVER_CONF, $MESSAGE, $ROOM_IMG, $RQ_ARGS;

  //¼���γ�ǧ
  $num_rooms = FetchResult("SELECT COUNT(*) FROM room WHERE status = 'finished'");
  if($num_rooms == 0){
    OutputActionResult($SERVER_CONF->title . $num_rooms . ' [����]', '���Ϥ���ޤ���<br>' . "\n" .
		       '<a href="index.php">�����</a>'."\n");
  }

  OutputHTMLHeader($SERVER_CONF->title . ' [����]', 'old_log_list');
echo <<<EOF
</head>
<body id="room_list">
<p><a href="index.php">�����</a></p>
<img src="img/old_log_title.jpg"><br>
<div align="center">
<table><tr><td class="list">
[�ڡ���]

EOF;

  $config =& new OldLogConfig(); //��������
  $is_reverse = (empty($reverse) ? $config->reverse : ($reverse == 'on'));

  //�ڡ�����󥯤ν���
  if(is_null($page)) $page = 1;
  $num_pages = ceil($num_rooms / $config->one_page) + 1; //[all] �ΰ٤� + 1 ���Ƥ���
  $url_option = '&reverse='.($is_reverse ? 'on' : 'off');
  if($RQ_ARGS->add_role) $url_option .= '&add_role=on';
  for($page_number = 1; $page_number <= $num_pages; $page_number++){
    $page_title = ($page_number == $num_pages ? 'all' : $page_number);
    if($page == $page_title)
      echo " [$page_title] ";
    else
      echo " <a href=\"old_log.php?page=$page_title$url_option\">[$page_title]</a> ";
  }
  $reverse_text = ($is_reverse xor $config->reverse) ? '�����᤹' : '�����ؤ���';
  $base_url = 'old_log.php?'.($RQ_ARGS->add_role ? '&add_role=on' : '').'&reverse=';
  if($is_reverse)
    echo 'ɽ����:������ <a href="'.$base_url.'off">'.$reverse_text.'</a>';
  else
    echo 'ɽ����:�Ţ��� <a href="'.$base_url.'on">'.$reverse_text.'</a>';

  $game_option_list = array('dummy_boy', 'open_vote', 'not_open_cast', 'decide',
			    'authority', 'poison', 'cupid', 'boss_wolf', 'poison_wolf',
			    'mania', 'medium', 'liar', 'gentleman', 'sudden_death',
			    'chaos', 'chaos_open_cast', 'secret_sub_role', 'no_sub_role');

  echo <<<EOF
</td></tr>
<!--¼���� ��������-->
<tr><td>
<table class="main">
<tr><th>¼No</th><th>¼̾</th><th>�Ϳ�</th><th>����</th><th>��</th></tr>

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
			AS room_num_user, victory_role AS room_victory_role,
			establish_time, start_time, finish_time FROM room
			WHERE status = 'finished' ORDER BY room_no $room_order $limit_statement");

  $victory_img =& new VictoryImage();
  while(($array = mysql_fetch_assoc($sql)) !== false){
    extract($array, EXTR_PREFIX_ALL, 'log');

    //���ץ����Ⱦ��Ԥβ���
    $game_option_str = MakeGameOptionImage($log_room_game_option, $log_room_option_role);
    $victory_role_str = $victory_img->MakeVictoryImage($log_room_victory_role);
    //��¼�ξ�硢���򳥿��ˤ���
    $dead_room_color = ($log_room_date == 0 ? ' style="color:silver"' : '');

    //�桼����������
    // $str_max_users = $ROOM_IMG->max_user_list[$log_room_max_user];
    $user_count = intval($log_room_num_user);

    $base_url = "old_log.php?room_no=$log_room_no";
    if($RQ_ARGS->add_role) $base_url .= '&add_role=on';

    /*
    if ($DEBUG_MODE){
      $debug_anchor = "<a href=\"$base_url&debug=on\" $dead_room_color >Ͽ</a>";
    }
    */

    if($log_establish_time != '') $log_establish_time = ConvertTimeStamp($log_establish_time);
    echo <<<EOF
<tr class="list">
<td class="number" rowspan="3">$log_room_no</td>
<td class="title"><a href="$base_url" $dead_room_color>$log_room_name ¼</a></td>
<td class="upper">$user_count (����{$log_room_max_user})</td>
<td class="upper">$log_room_date</td>
<td class="side">$victory_role_str</td>
</tr>
<tr class="list middle">
<td class="comment side">�� $log_room_comment ��</td>
<td class="time comment" colspan="3">$log_establish_time</td>
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
<td colspan="3">$game_option_str</td>
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
function OutputOldLog(){
  global $SERVER_CONF, $RQ_ARGS, $ROOM, $room_no, $last_date;

  $base_title = $SERVER_CONF->title . ' [����]';
  $url = "<br>\n<a href=\"old_log.php\">�����</a>\n";

  if(is_null($room_no)) OutputActionResult($title, '¼����ꤷ�Ƥ���������' . $url);

  //���դȥ���������
  $ROOM = new RoomDataSet($RQ_ARGS);
  $ROOM->log_mode = true;
  $last_date = $ROOM->date;

  if(! $ROOM->IsFinished() || ! $ROOM->IsAfterGame()){
    OutputActionResult($base_title, '�ޤ����������Υ��ϱ����Ǥ��ޤ���' . $url);
  }

  $title = '[' . $room_no . '����] ' . $ROOM->name . ' - ' . $base_title;

  //���������Υڡ����ˤ���
  $referer_page_str = strstr($_SERVER['HTTP_REFERER'], 'page');
  sscanf($referer_page_str, "page=%s", &$referer_page);

  OutputHTMLHeader($title, 'old_log');
  echo <<<EOF
</head>
<body>
<a href="old_log.php?page=$referer_page">�����</a><br>
<div class="room"><span>{$ROOM->name}¼</span>����{$ROOM->comment}�� [{$room_no}����]</td></div>

EOF;
  OutputPlayerList();   //�ץ쥤�䡼�ꥹ�Ȥ����

  $layout = 'Layout'.($RQ_ARGS->heaven_only ? 'Heaven' : 'TalkLog');
  $layout($last_date, $RQ_ARGS->reverse_log);
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
  global $RQ_ARGS, $ROLES, $room_no, $ROOM, $last_date;

  if($is_reverse) //�ս硢��������ǽ����ޤ�
    $select_order = 'ORDER BY time';
  else //�ǽ�����������ޤ�
    $select_order = 'ORDER BY time DESC';

  switch($set_location){
  case 'beforegame':
  case 'aftergame':
    $table_class = $set_location;
    $date_select = '';
    $location_select = "AND location LIKE '$set_location%'";
    break;

  default:
    //�����ܰʹߤ��뤫��Ϥޤ�
    $table_class = ($is_reverse && $set_date != 1) ? 'day' : 'night';
    $date_select = "AND date = $set_date";
    if($set_location == 'heaven_only')
      $location_select = "AND (location = 'heaven' OR uname = 'system')";
    else
      $location_select = "AND location <> 'aftergame' AND location <> 'beforegame'";
    break;
  }

  $flag_border_game = false;
  //���äΥ桼��̾���ϥ�ɥ�̾��ȯ����ȯ���Υ����פ����
  $query = "SELECT uname, sentence, font_type, location FROM talk WHERE room_no = $room_no AND ";
  if($set_location == 'heaven_only'){
    $query .= "date = $set_date AND (location = 'heaven' OR uname = 'system')";
  }
  elseif($set_location == 'beforegame' || $set_location == 'aftergame'){
    $query .= "location like '$set_location%'";
  }
  else{
    $flag_border_game = true;
    $query .= "date = $set_date AND location <> 'beforegame' AND location <> 'aftergame'";
    if(! $RQ_ARGS->heaven_talk) $query .= " AND location <> 'heaven'";
  }
  $sql = mysql_query("$query $select_order");

  if($flag_border_game && ! $is_reverse && $set_date != $last_date){
    $ROOM->date = $set_date + 1;
    $ROOM->day_night = 'day';
    OutputLastWords(); //��������
    OutputDeadMan();   //��˴�Ԥ����
  }
  $ROOM->date = $set_date;
  $ROOM->day_night = $table_class;

  //����
  $builder = DocumentBuilder::Generate();
  $builder->BeginTalk("old-log-talk {$table_class}");
  while(($talk = mysql_fetch_object($sql, 'Talk')) !== false){
    if(strpos($talk->location, 'day') !== false && ! $ROOM->IsDay()){
      $builder->EndTalk();
      OutputSceneChange($set_date);
      $ROOM->day_night = 'day';
      $builder->BeginTalk('old-log-talk day');
    }
    elseif(strpos($talk->location, 'night') !== false && ! $ROOM->IsNight()){
      $builder->EndTalk();
      OutputSceneChange($set_date);
      $ROOM->day_night = 'night';
      $builder->BeginTalk('old-log-talk night');
    }
    OutputTalk($talk, &$builder); //���ý���
  }
  $builder->EndTalk();

  if($flag_border_game && $is_reverse){
    if($set_date == $last_date && $ROOM->IsDay()){
      OutputVoteList(); //������Ǿ��Ԥ����ꤷ��������
    }
    $ROOM->date = $set_date + 1;
    $ROOM->day_night = 'day';
    OutputDeadMan();   //��˴�Ԥ����
    OutputLastWords(); //��������
  }
}

//�������ڤ��ؤ����Υ�����
function OutputSceneChange($set_date){
  global $RQ_ARGS, $ROOM;

  if($RQ_ARGS->heaven_only) return;
  $ROOM->date = $set_date;
  if($RQ_ARGS->reverse_log){
    $ROOM->day_night = 'night';
    OutputVoteList(); //��ɼ��̽���
    OutputDeadMan();  //��˴�Ԥ����
  }
  else{
    OutputDeadMan();  //��˴�Ԥ����
    OutputVoteList(); //��ɼ��̽���
  }
}
?>