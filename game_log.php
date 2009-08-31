<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');
require_once(dirname(__FILE__) . '/include/request_class.php');

//���������
$RQ_ARGS = new RequestGameLog();
if($RQ_ARGS->day_night != 'day' && $RQ_ARGS->day_night != 'night'){
  OutputActionResult('�������顼', '�������顼��̵���ʰ����Ǥ�');
}
$room_no = $RQ_ARGS->room_no;

//���å���󳫻�
session_start();
$session_id = session_id();

$dbHandle = ConnectDatabase(); //DB ��³
$uname = CheckSession($session_id); //���å���� ID ������å�

$ROOM = new RoomDataSet($RQ_ARGS); //������������
$ROOM->log_mode = true;

//��ʬ�Υϥ�ɥ�͡��ࡢ��䡢��¸�����
$USERS = new UserDataSet($RQ_ARGS); //�桼����������
$SELF  = $USERS->ByUname($uname);

if(! ($SELF->is_dead() || $ROOM->is_aftergame())){ //��Ԥ������ཪλ�����
  OutputActionResult('�桼��ǧ�ڥ��顼',
		     '���������ĥ��顼<br>' .
		     '<a href="index.php" target="_top">�ȥåץڡ���</a>' .
		     '��������󤷤ʤ����Ƥ�������');
}
$ROOM->date      = $RQ_ARGS->date;
$ROOM->day_night = $RQ_ARGS->day_night;

OutputGamePageHeader(); //HTML�إå�
echo '<table><tr><td width="1000" align="right">������ ' . $ROOM->date . ' ���� (' .
  ($ROOM->is_day() ? '��' : '��') . ')</td></tr></table>'."\n";
OutputTalkLog();       //���å�
OutputAbilityAction(); //ǽ��ȯ��
OutputDeadMan();       //��˴��
if($ROOM->is_night()) OutputVoteList(); //��ɼ���
OutputHTMLFooter();
DisconnectDatabase($dbHandle); //DB ��³���
?>
