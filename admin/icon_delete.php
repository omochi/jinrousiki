<?php
require_once(dirname(__FILE__) . '/../include/functions.php');

$CSS_PATH = '../css'; //CSS �Υѥ�����

if(! $DEBUG_MODE)
  OutputActionResult('ǧ�ڥ��顼', '���Υ�����ץȤϻ��ѤǤ��ʤ�����ˤʤäƤ��ޤ���');

$icon_no = (int)$_GET['icon_no'];
$dbHandle = ConnectDatabase(); //DB ��³
$sql = mysql_query("SELECT icon_filename, session_id FROM user_icon WHERE icon_no = $icon_no");
$array = mysql_fetch_assoc($sql);
$file  = $array['icon_filename'];
unlink('../' . $ICON_CONF->path . '/' . $file);
mysql_query("DELETE FROM user_icon WHERE icon_no = $icon_no");
mysql_query('COMMIT'); //������ߥå�

//DB ��³����� OutputActionResult() ��ͳ
OutputActionResult('������������λ',
		   '�����λ����Ͽ�ڡ��������Ӥޤ���<br>'."\n" .
		   '�ڤ��ؤ��ʤ��ʤ� <a href="../icon_upload.php">����</a> ��',
		   '../icon_upload.php');
?>
