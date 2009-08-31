<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');
require_once(dirname(__FILE__) . '/include/request_class.php');

//���������
$RQ_ARGS = new RequestGameView();
$room_no = $RQ_ARGS->room_no;
$url = 'game_view.php?room_no=' . $room_no;

$dbHandle = ConnectDatabase(); // DB ��³

$ROOM = new RoomDataSet($RQ_ARGS); //¼��������
$ROOM->view_mode = true;
$ROOM->system_time = TZTime(); //���߻�������
switch($ROOM->day_night){
case 'day': //��
  $time_message = '�����פޤ� ';
  break;

case 'night': //��
  $time_message = '���������ޤ� ';
  break;
}
$USERS = new UserDataSet($RQ_ARGS); //�桼����������
$SELF  = new User();

OutputHTMLHeader('��Ͽ�ϵ�ʤ�䡩[����]', 'game_view'); //HTML�إå�

if($GAME_CONF->auto_reload && $RQ_ARGS->auto_reload != 0){ //��ư����
  echo '<meta http-equiv="Refresh" content="' . $RQ_ARGS->auto_reload . '">'."\n";
}

//������˹�碌��ʸ�������طʿ� CSS �����
echo '<link rel="stylesheet" href="css/game_' . $ROOM->day_night . '.css">'."\n";

//�в���֤����
if($ROOM->is_real_time()){ //�ꥢ�륿������
  list($start_time, $end_time) = GetRealPassTime(&$left_time, true);
  if($ROOM->is_playing()){
    $on_load = ' onLoad="output_realtime();"';
    OutputRealTimer($start_time, $end_time);
  }
}
else{ //���äǻ��ַв���
  $left_talk_time = GetTalkPassTime(&$left_time);
}

echo <<<EOF
</head>
<body{$on_load}>
<a name="#game_top"></a>
<table class="login"><tr>
<td classs="room"><span>{$ROOM->name}¼</span>����{$ROOM->comment}��[{$room_no}����]</td>
<td class="login-link">

EOF;

if($GAME_CONF->auto_reload){ //��ư�������꤬ͭ���ʤ��󥯤�ɽ��
  echo '<a href="' . $url . '&auto_reload=' . $RQ_ARGS->auto_reload . '">[����]</a>'."\n";
  OutputAutoReloadLink('<a href="' . $url . '&auto_reload=');
}
else{
  echo '<a href="' . $url . '">[����]</a>'."\n";
}

echo <<<EOF
<a href="index.php">[���]</a>
</td></tr>
<tr><td><form method="POST" action="login.php?room_no=$room_no">
<label>�桼��̾</label><input type="text" name="uname" size="20">
<label>�ѥ����</label><input type="password" class="login-password" name="password" size="20">
<input type="hidden" name="login_type" value="manually">
<input type="submit" value="������">
</form></td>

EOF;

if($ROOM->is_beforegame()){ //�����೫�����ʤ���Ͽ���̤Υ�󥯤�ɽ��
  echo '<td class="login-link">';
  echo '<a href="user_manager.php?room_no=' . $room_no . '"><span>[��̱��Ͽ]</span></a>';
  echo '</td>'."\n";
}
echo '</tr></table>'."\n";

if(! $ROOM->is_finished()) OutputGameOption(); //�����४�ץ�����ɽ��

echo '<table class="time-table"><tr>'."\n";
OutputTimeTable(); //�в���������¸�Ϳ�

if($ROOM->is_playing()){
  if($ROOM->is_real_time()){ //�ꥢ�륿������
    echo '<td class="real-time"><form name="realtime_form">'."\n";
    echo '<input type="text" name="output_realtime" size="50" readonly>'."\n";
    echo '</form></td>'."\n";
  }
  elseif($left_time){ //���äǻ��ַв���
    echo '<td>' . $time_message . $left_talk_time . '</td>'."\n";
  }

  if($left_time == 0){
    echo '</tr><tr>'."\n" . '<td class="system-vote" colspan="2">' . $time_message .
      $MESSAGE->vote_announce . '</td>'."\n";
  }
}
echo '</tr></table>'."\n";

OutputPlayerList(); //�ץ쥤�䡼�ꥹ��
if($ROOM->is_finished()) OutputVictory(); //���Է��
OutputRevoteList(); //����ɼ��å�����
OutputTalkLog();    //���å�
OutputLastWords();  //���
OutputDeadMan();    //��˴��
OutputVoteList();   //��ɼ���
OutputHTMLFooter(); //HTML�եå�

DisconnectDatabase($dbHandle); //DB ��³���
?>
