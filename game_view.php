<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('user_class', 'talk_class');
$INIT_CONF->LoadClass('ROLES', 'ICON_CONF');

//-- �ǡ������� --//
$INIT_CONF->LoadRequest('RequestBaseGame'); //���������
$url = 'game_view.php?room_no=' . $RQ_ARGS->room_no;

$DB_CONF->Connect(); // DB ��³

$ROOM =& new Room($RQ_ARGS); //¼��������
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

//������˱������ɲå��饹�����
if($ROOM->IsFinished()){
  $INIT_CONF->LoadClass('VICT_MESS');
}
else{
  $INIT_CONF->LoadClass('CAST_CONF', 'ROOM_IMG', 'GAME_OPT_MESS');
}

$USERS =& new UserDataSet($RQ_ARGS); //�桼����������
$SELF  =& new User();
if($ROOM->IsBeforeGame()) $ROOM->LoadVote();

//-- �ǡ������� --//
OutputHTMLHeader($SERVER_CONF->title . '[����]', 'game_view'); //HTML�إå�

if($GAME_CONF->auto_reload && $RQ_ARGS->auto_reload != 0){ //��ư����
  echo '<meta http-equiv="Refresh" content="' . $RQ_ARGS->auto_reload . '">'."\n";
}

//������˹�碌��ʸ�������طʿ� CSS �����
echo '<link rel="stylesheet" href="css/game_' . $ROOM->day_night . '.css">'."\n";

if($ROOM->IsPlaying()){ //�в���֤����
  if($ROOM->IsRealTime()){ //�ꥢ�륿������
    list($start_time, $end_time) = GetRealPassTime(&$left_time, true);
    $on_load = ' onLoad="output_realtime();"';
    OutputRealTimer($start_time, $end_time);
  }
  else{ //���äǻ��ַв���
    $INIT_CONF->LoadClass('TIME_CONF');
    $left_talk_time = GetTalkPassTime(&$left_time);
  }
}

echo <<<EOF
</head>
<body{$on_load}>
<a name="#game_top"></a>
<table class="login"><tr>
<td classs="room"><span>{$ROOM->name}¼</span>����{$ROOM->comment}��[{$ROOM->id}����]</td>
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
<a href="./">[���]</a>
</td></tr>
<tr><td><form method="POST" action="login.php?room_no={$ROOM->id}">
<label>�桼��̾</label><input type="text" name="uname" size="20">
<label>�ѥ����</label><input type="password" class="login-password" name="password" size="20">
<input type="hidden" name="login_manually" value="on">
<input type="submit" value="������">
</form></td>

EOF;

if($ROOM->IsBeforeGame()){ //�����೫�����ʤ���Ͽ���̤Υ�󥯤�ɽ��
  echo '<td class="login-link">';
  echo '<a href="user_manager.php?room_no=' . $ROOM->id . '"><span>[��̱��Ͽ]</span></a>';
  echo '</td>'."\n";
}
echo '</tr></table>'."\n";


if(! $ROOM->IsFinished()){
  OutputGameOption(); //�����४�ץ�����ɽ��
}

echo '<table class="time-table"><tr>'."\n";
OutputTimeTable(); //�в���������¸�Ϳ�

if($ROOM->IsPlaying()){
  if($ROOM->IsRealTime()){ //�ꥢ�륿������
    echo '<td class="real-time"><form name="realtime_form">'."\n";
    echo '<input type="text" name="output_realtime" size="50" readonly>'."\n";
    echo '</form></td>'."\n";
  }
  elseif($left_talk_time){ //���äǻ��ַв���
    echo '<td>' . $time_message . $left_talk_time . '</td>'."\n";
  }

  if($left_time == 0){
    echo '</tr><tr>'."\n" . '<td class="system-vote" colspan="2">' . $time_message .
      $MESSAGE->vote_announce . '</td>'."\n";
  }
}
echo '</tr></table>'."\n";

OutputPlayerList(); //�ץ쥤�䡼�ꥹ��
if($ROOM->IsFinished()) OutputVictory(); //���Է��
OutputRevoteList(); //����ɼ��å�����
OutputTalkLog();    //���å�
OutputLastWords();  //���
OutputDeadMan();    //��˴��
OutputVoteList();   //��ɼ���
OutputHTMLFooter(); //HTML�եå�
