<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');

if(! $DEBUG_MODE){
  OutputActionResult('ǧ�ڥ��顼', '���Υ�����ץȤϻ��ѤǤ��ʤ�����ˤʤäƤ��ޤ���');
}

extract($_GET, EXTR_PREFIX_ALL, 'unsafe');
$room_no = intval($unsafe_room_no);
if($room_no < 1) OutputActionResult('�������[���顼]', '̵����¼�ֹ�Ǥ���');

$DB_CONF->Connect(); //DB ��³
mysql_query(sprintf("DELETE FROM room WHERE room_no=%d", $room_no));
mysql_query(sprintf("DELETE FROM system_message WHERE room_no=%d", $room_no));
mysql_query(sprintf("DELETE FROM talk WHERE room_no=%d", $room_no));
mysql_query(sprintf("DELETE FROM user_entry WHERE room_no=%d", $room_no));
mysql_query(sprintf("DELETE FROM vote WHERE room_no=%d", $room_no));
mysql_query("OPTIMIZE TABLE room, system_message , talk, user_entry, vote");
OutputActionResult('�������', $room_no . ' ���Ϥ������ޤ������ȥåץڡ��������ޤ���', '../');
