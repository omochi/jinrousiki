<?php
require_once(dirname(__FILE__) . '/../include/functions.php');

$CSS_PATH = '../css'; //CSS �Υѥ�����

if(! $DEBUG_MODE)
  OutputActionResult('ǧ�ڥ��顼', '���Υ�����ץȤϻ��ѤǤ��ʤ�����ˤʤäƤ��ޤ���');

extract($_GET, EXTR_PREFIX_ALL, 'unsafe');
$room_no = intval($unsafe_room_no);
if($room_no < 1) OutputActionResult('�������[���顼]', '̵����¼�ֹ�Ǥ���');

$dbHandle = ConnectDatabase(); //DB ��³
mysql_query(sprintf("DELETE FROM talk WHERE room_no=%d", $room_no));
mysql_query(sprintf("DELETE FROM system_message WHERE room_no=%d", $room_no));
mysql_query(sprintf("DELETE FROM vote WHERE room_no=%d", $room_no));
mysql_query(sprintf("DELETE FROM user_entry WHERE room_no=%d", $room_no));
mysql_query(sprintf("DELETE FROM room WHERE room_no=%d", $room_no));

OutputActionResult('�������',
		   $room_no . ' ���Ϥ������ޤ������ȥåץڡ��������ޤ���',
		   '../index.php');
?>