<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');

if(! $DEBUG_MODE){
  OutputActionResult('ǧ�ڥ��顼', '���Υ�����ץȤϻ��ѤǤ��ʤ�����ˤʤäƤ��ޤ���');
}
$INIT_CONF->LoadClass('ICON_CONF');

$DB_CONF->Connect(); //DB ��³
$icon_no = (int)$_GET['icon_no'];
$file = FetchResult("SELECT icon_filename FROM user_icon WHERE icon_no = $icon_no");

unlink($ICON_CONF->path . '/' . $file); //�ե������¸�ߤ�����å����Ƥ��ʤ��Τ������
mysql_query("DELETE FROM user_icon WHERE icon_no = $icon_no");
mysql_query('COMMIT'); //������ߥå�

//DB ��³����� OutputActionResult() ��ͳ
OutputActionResult('������������λ',
		   '�����λ����Ͽ�ڡ��������Ӥޤ���<br>'."\n" .
		   '�ڤ��ؤ��ʤ��ʤ� <a href="../icon_upload.php">����</a> ��',
		   '../icon_upload.php');
