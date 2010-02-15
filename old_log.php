<?php
require_once('include/init.php');

$INIT_CONF->LoadRequest('RequestOldLog'); //���������

$DB_CONF->Connect(); //DB ��³
if($RQ_ARGS->is_room){
  $INIT_CONF->LoadFile('game_play_functions', 'user_class', 'talk_class');
  $INIT_CONF->LoadClass('ROLES', 'ICON_CONF', 'VICT_MESS');

  $ROOM =& new Room($RQ_ARGS);
  $ROOM->log_mode = true;
  $ROOM->last_date = $ROOM->date;

  $USERS =& new UserDataSet($RQ_ARGS);
  $SELF  =& new User();

  OutputOldLog();
}
else{
  $INIT_CONF->LoadClass('ROOM_CONF', 'ROOM_IMG', 'GAME_OPT_MESS');
  OutputFinishedRooms($RQ_ARGS->page, $RQ_ARGS->reverse);
}
OutputHTMLFooter();

// �ؿ� //
//��������ɽ��
function OutputFinishedRooms($page, $reverse = NULL){
  global $SERVER_CONF, $ROOM_CONF, $MESSAGE, $ROOM_IMG, $RQ_ARGS;

  //¼���γ�ǧ
  $room_count = FetchResult("SELECT COUNT(*) FROM room WHERE status = 'finished'");
  if($room_count < 1){
    OutputActionResult($SERVER_CONF->title . ' [����]',
		       '���Ϥ���ޤ���<br>'."\n" . '<a href="./">�����</a>'."\n");
  }

  OutputHTMLHeader($SERVER_CONF->title . ' [����]', 'old_log_list');
echo <<<EOF
</head>
<body id="room_list">
<p><a href="index.php">�����</a></p>
<img src="img/old_log_title.jpg"><br>
<div align="center">
<table><tr><td class="list">

EOF;

  $config =& new OldLogConfig(); //��������
  $is_reverse = empty($reverse) ? $config->reverse : ($reverse == 'on');
  $current_time = TZTime(); // ���߻���μ���

  //�ڡ�����󥯤ν���
  if(is_null($page)) $page = 1;
  $page_count = ceil($room_count / $config->room);
  $start_page = $page;
  if($page_count - $page < $config->page){
    $start_page = $page_count - $config->page + 1;
  }
  $end_page = $page + $config->page - 1;
  if($end_page > $page_count) $end_page = $page_count;

  $url_stack = array('[�ڡ���]');
  $url_header = '<a href="old_log.php?';
  $url_option = array('reverse='. ($is_reverse ? 'on' : 'off'));
  if($RQ_ARGS->add_role) $url_option[] = 'add_role=on';

  if($page_count > $config->page && $page > 1){
    $url_stack[] = GenerateLinkStack($url_option, 1, '[1]...');
    $url_stack[] = GenerateLinkStack($url_option, $start_page - 1, '&lt;&lt;');
  }

  for($page_number = $start_page; $page_number <= $end_page; $page_number++){
    if($page == $page_number)
      $url_stack[] = "[$page_number]";
    else
      $url_stack[] = GenerateLinkStack($url_option, $page_number);
  }

  if($page_number <= $page_count){
    $url_stack[] = GenerateLinkStack($url_option, $page_number, '&gt;&gt;');
    $url_stack[] = GenerateLinkStack($url_option, $page_count, '...[' . $page_count . ']');
  }
  $url_stack[] = GenerateLinkStack($url_option, 'all');

  $list = $url_option;
  $list[] = 'reverse=' . ($is_reverse ? 'off' : 'on');
  $url_stack[] = '[ɽ����]';
  $url_stack[] = ($is_reverse ? '������' : '�Ţ���');

  $url = $url_header . implode('&', $list) . '">';
  $url_stack[] =  $url . (($is_reverse xor $config->reverse) ? '�����᤹' : '�����ؤ���') . '</a>';
  echo implode(' ', $url_stack);

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
    $start_number = $config->room * ($page - 1);
    $limit_statement = sprintf('LIMIT %d, %d', $start_number, $config->room);
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

  $VICT_IMG =& new VictoryImage();
  while(($array = mysql_fetch_assoc($sql)) !== false){
    extract($array, EXTR_PREFIX_ALL, 'log');

    //���ץ����Ⱦ��Ԥβ���
    $game_option_str = GenerateGameOptionImage($log_room_game_option, $log_room_option_role);
    $victory_role_str = $VICT_IMG->Generate($log_room_victory_role);
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
<td class="title"><a href="$base_url" $dead_room_color>$log_room_name ¼</a>
<td class="upper">$user_count (����{$log_room_max_user})</td>
<td class="upper">$log_room_date</td>
<td class="side">$victory_role_str</td>
</tr>
<tr class="list middle">
<td class="comment side">�� $log_room_comment ��</td>
<td class="time comment" colspan="3">$log_establish_time</td>
</tr>
<tr class="lower list">
<td class="comment">

EOF;

    $diff_time = $current_time - strtotime($log_finish_time);
    if($diff_time <= $ROOM_CONF->clear_session_id){
      echo <<<EOF
<a href="login.php?room_no=$log_room_no" $dead_room_color>[����¼]</a>

EOF;
    }
    echo <<<EOF
(
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

function GenerateLinkStack($list, $page, $title = NULL){
  $header = '<a href="old_log.php?';
  array_unshift($list, 'page=' . $page);
  if(is_null($title)) $title = '[' . $page . ']';
  return $header . implode('&', $list) . '">' . $title . '</a>';
}

//����������ֹ�Υ�����Ϥ���
function OutputOldLog(){
  global $SERVER_CONF, $RQ_ARGS, $ROOM;

  //�ѿ��򥻥å�
  $base_title = $SERVER_CONF->title . ' [����]';
  $url = "<br>\n<a href=\"old_log.php\">�����</a>\n";

  if(! $ROOM->IsFinished() || ! $ROOM->IsAfterGame()){
    OutputActionResult($base_title, '�ޤ����������Υ��ϱ����Ǥ��ޤ���' . $url);
  }
  $title = '[' . $ROOM->id . '����] ' . $ROOM->name . ' - ' . $base_title;

  //���������Υڡ����ˤ���
  $referer_url = sprintf("%s", $_SERVER['HTTP_REFERER']);
  if(strpos($referer_url, $SERVER_CONF->site_root . 'old_log.php') === 0){
    $referer = $referer_url;
  }
  else{
    $referer = 'old_log.php';
  }

  OutputHTMLHeader($title, 'old_log');
  echo <<<EOF
</head>
<body>
<a href="{$referer}">�����</a><br>
<div class="room"><span>{$ROOM->name}¼</span> ��{$ROOM->comment}�� [{$ROOM->id}����]</td></div>

EOF;
  OutputPlayerList(); //�ץ쥤�䡼�ꥹ�Ȥ����
  $RQ_ARGS->heaven_only ? LayoutHeaven() : LayoutTalkLog();
}

//�̾�Υ�ɽ�����ɽ�����ޤ���
function LayoutTalkLog(){
  global $RQ_ARGS, $ROOM;

  if($RQ_ARGS->reverse_log){
    OutputDateTalkLog(0, 'beforegame');
    for($i = 1; $i <= $ROOM->last_date; $i++){
      OutputDateTalkLog($i, '');
    }
    OutputVictory();
    OutputDateTalkLog($ROOM->last_date, 'aftergame');
  }
  else{
    OutputDateTalkLog($ROOM->last_date, 'aftergame');
    OutputVictory();
    for($i = $ROOM->last_date; $i > 0; $i--){
      OutputDateTalkLog($i, '');
    }
    OutputDateTalkLog(0, 'beforegame');
  }
}

//��ΤߤΥ�ɽ�����ɽ�����ޤ���
function LayoutHeaven(){
  global $RQ_ARGS, $ROOM;

  if($RQ_ARGS->reverse_log){
    for($i = 1; $i <= $ROOM->last_date; $i++){
      OutputDateTalkLog($i, 'heaven_only');
    }
  }
  else{
    for($i = $ROOM->last_date; $i > 0; $i--){
      OutputDateTalkLog($i, 'heaven_only');
    }
  }
}

//��������դβ��å������
function OutputDateTalkLog($set_date, $set_location){
  global $RQ_ARGS, $ROLES, $ROOM;

  if($RQ_ARGS->reverse_log) //�ս硢��������ǽ����ޤ�
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
    $table_class = ($RQ_ARGS->reverse_log && $set_date != 1) ? 'day' : 'night';
    $date_select = "AND date = $set_date";
    if($set_location == 'heaven_only')
      $location_select = "AND (location = 'heaven' OR uname = 'system')";
    else
      $location_select = "AND location <> 'aftergame' AND location <> 'beforegame'";
    break;
  }

  $flag_border_game = false;
  //���äΥ桼��̾���ϥ�ɥ�̾��ȯ����ȯ���Υ����פ����
  $query = "SELECT uname, sentence, font_type, location FROM talk WHERE room_no = {$ROOM->id} AND ";
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

  //-- ���۲�ư�⡼�ɥƥ����� --//
  //global $USERS, $SELF;
  //$SELF = $USERS->rows[3];
  //$SELF->ParseRoles('human earplug');
  //$SELF->live = 'live';
  //$ROOM->status = 'playing';
  //$ROOM->option_list[] = 'not_open_cast';

  if($flag_border_game && ! $RQ_ARGS->reverse_log && $set_date != $ROOM->last_date){
    $ROOM->date = $set_date + 1;
    $ROOM->day_night = 'day';
    OutputLastWords(); //��������
    OutputDeadMan();   //��˴�Ԥ����
  }
  $ROOM->date = $set_date;
  $ROOM->day_night = $table_class;

  //����
  $builder =& new DocumentBuilder();
  $builder->BeginTalk("old-log-talk {$table_class}");
  if($RQ_ARGS->reverse_log) OutputTimeStamp($builder);

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

  if(! $RQ_ARGS->reverse_log) OutputTimeStamp($builder);
  $builder->EndTalk();

  if($flag_border_game && $RQ_ARGS->reverse_log){
    if($set_date == $ROOM->last_date && $ROOM->IsDay()){
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
