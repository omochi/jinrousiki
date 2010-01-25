<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('user_class', 'talk_class');
$INIT_CONF->LoadClass('ROLES');

//���������
$RQ_ARGS =& new RequestGameLog();
if($RQ_ARGS->day_night != 'day' && $RQ_ARGS->day_night != 'night' &&
   ! ($RQ_ARGS->day_night == 'beforegame' && $RQ_ARGS->date == 0)){
  OutputActionResult('�������顼', '�������顼��̵���ʰ����Ǥ�');
}

//���å���󳫻�
session_start();
$session_id = session_id();

$dbHandle = ConnectDatabase(); //DB ��³
$uname = CheckSession($session_id); //���å���� ID ������å�

$ROOM =& new RoomDataSet($RQ_ARGS); //������������
$ROOM->log_mode = true;

$USERS =& new UserDataSet($RQ_ARGS); //�桼����������
$SELF = $USERS->ByUname($uname);

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
OutputLastWords();     //���
OutputDeadMan();       //��˴��
if($ROOM->IsNight()) OutputVoteList(); //��ɼ���
OutputHTMLFooter();
DisconnectDatabase($dbHandle); //DB ��³���
?>
