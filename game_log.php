<?php
require_once(dirname(__FILE__) . '/include/init.php');
loadModule(
  CONFIG,
  IMAGE_CLASSES,
  ROLE_CLASSES,
  MESSAGE_CLASSES,
  GAME_FORMAT_CLASSES,
  SYSTEM_CLASSES,
  USER_CLASSES,
  TALK_CLASSES,
  GAME_FUNCTIONS,
  #PLAY_FUNCTIONS,
  #VOTE_FUNCTIONS,
  ROOM_IMG,
  ROLE_IMG,
  ROOM_CONF,
  GAME_CONF,
  TIME_CONF,
  ICON_CONF,
  ROLES,
  MESSAGE
  );

//���������
$RQ_ARGS = new RequestGameLog();
if($RQ_ARGS->day_night != 'day' && $RQ_ARGS->day_night != 'night' &&
   ! ($RQ_ARGS->day_night == 'beforegame' && $RQ_ARGS->date == 0)){
  OutputActionResult('�������顼', '�������顼��̵���ʰ����Ǥ�');
}

//���å���󳫻�
session_start();
$session_id = session_id();

$dbHandle = ConnectDatabase(); //DB ��³
$uname = CheckSession($session_id); //���å���� ID ������å�

$ROOM = new RoomDataSet($RQ_ARGS); //������������
$ROOM->log_mode = true;

$USERS = new UserDataSet($RQ_ARGS); //�桼����������
$SELF  = $USERS->ByUname($uname);

if(! ($SELF->IsDead() || $ROOM->IsAfterGame())){ //��Ԥ������ཪλ�����
  OutputActionResult('�桼��ǧ�ڥ��顼',
		     '���������ĥ��顼<br>' .
		     '<a href="index.php" target="_top">�ȥåץڡ���</a>' .
		     '��������󤷤ʤ����Ƥ�������');
}
$ROOM->date      = $RQ_ARGS->date;
$ROOM->day_night = $RQ_ARGS->day_night;

OutputGamePageHeader(); //HTML�إå�
echo '<table><tr><td width="1000" align="right">������ ' . $ROOM->date . ' ���� (' .
  ($ROOM->IsBeforeGame() ? '������' : ($ROOM->IsDay() ? '��' : '��')) . ')</td></tr></table>'."\n";
OutputTalkLog();       //���å�
OutputAbilityAction(); //ǽ��ȯ��
OutputDeadMan();       //��˴��
if($ROOM->IsNight()) OutputVoteList(); //��ɼ���
OutputHTMLFooter();
DisconnectDatabase($dbHandle); //DB ��³���
?>
