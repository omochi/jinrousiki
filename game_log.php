<?php
require_once(dirname(__FILE__) . '/include/game_functions.php');

//���å���󳫻�
session_start();
$session_id = session_id();

//���������
$room_no       = (int)$_GET['room_no'];
$log_mode      = $_GET['log_mode'];
$get_date      = (int)$_GET['date'];
$get_day_night = $_GET['day_night'];
if($get_day_night != 'day' && $get_day_night != 'night'){
  OutputActionResult('�������顼', '�������顼<br>̵���ʰ����Ǥ�');
}

$dbHandle = ConnectDatabase(); //DB ��³
$uname = CheckSession($session_id); //���å���� ID ������å�

//���դȥ���������
$ROOM = new RoomDataSet($room_no);
$date        = $ROOM->date;
$day_night   = $ROOM->day_night;
$game_option = $ROOM->game_option;

//��ʬ�Υϥ�ɥ�͡��ࡢ��䡢��¸�����
$USERS = new UserDataSet($room_no); //�桼����������
$user_no     = $USERS->UnameToNumber($uname);
$handle_name = $USERS->rows[$user_no]->handle_name;
$sex         = $USERS->rows[$user_no]->sex;
$role        = $USERS->rows[$user_no]->role;
$live        = $USERS->rows[$user_no]->live;

if($live != 'dead' && ! $ROOM->is_aftergame()){ //��Ԥ������ཪλ�����
  OutputActionResult('�桼��ǧ�ڥ��顼',
		     '���������ĥ��顼<br>' .
		     '<a href="index.php" target="_top">�ȥåץڡ���</a>' .
		     '��������󤷤ʤ����Ƥ�������');
}

$live = 'dead';
$ROOM->date = $get_date;
$ROOM->day_night = $get_day_night;

OutputGamePageHeader(); //HTML�إå�
echo '<table><tr><td width="1000" align="right">������ ' . $ROOM->date . ' ���� (' .
  ($ROOM->is_day() ? '��' : '��') . ')</td></tr></table>'."\n";
//OutputPlayerList();    //�ץ쥤�䡼�ꥹ��
OutputTalkLog();       //���å�
OutputAbilityAction(); //ǽ��ȯ��
OutputDeadMan();       //��˴��
if($ROOM->is_night()) OutputVoteList(); //��ɼ���
OutputHTMLFooter();
DisconnectDatabase($dbHandle); //DB ��³���
?>
