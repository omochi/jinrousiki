<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('ICON_CONF', 'USER_ICON');

if($USER_ICON->disable_upload){
  OutputActionResult('�桼���������󥢥åץ���', '���ߥ��åץ��ɤ���ߤ��Ƥ��ޤ�');
}

//��ե�������å�
$icon_upload_check_page_url = $SERVER_CONF->site_root . 'icon_upload_check.php';
if(strncmp(@$_SERVER['HTTP_REFERER'], $icon_upload_check_page_url,
	   strlen($icon_upload_check_page_url)) != 0){
  OutputActionResult('����������Ͽ��λ�ڡ���[���顼]', '̵���ʥ��������Ǥ���');
}

$icon_no = (int)$_POST['icon_no'];
switch($_POST['entry']){
case 'success': //���å����ID�����DB������
  $DB_CONF->Connect(); //DB ��³

  //���å����ID�򥯥ꥢ
  mysql_query("UPDATE user_icon SET session_id = NULL WHERE icon_no = $icon_no");
  mysql_query('COMMIT');

  OutputActionResult('����������Ͽ��λ',
		     '��Ͽ��λ��������������Υڡ��������Ӥޤ���<br>'."\n" .
		     '�ڤ��ؤ��ʤ��ʤ� <a href="icon_view.php">����</a> ��',
		     'icon_view.php');
  break;

case 'cancel': //DB���饢������Υե�����̾����Ͽ���Υ��å����ID�����
  $DB_CONF->Connect(); //DB ��³

  $array = FetchArray("SELECT icon_filename, session_id FROM user_icon WHERE icon_no = $icon_no");
  $file       = $array['icon_filename'];
  $session_id = $array['session_id'];

  //���å���󥹥�����
  session_start();
  if($session_id != session_id()){
    OutputActionResult('��������������',
		       '������ԡ����åץ��ɥ��å���󤬰��פ��ޤ���<br>'."\n" .
		       '<a href="index.php">�ȥåץڡ��������</a>');
  }
  unlink($ICON_CONF->path . '/' . $file);
  mysql_query("DELETE FROM user_icon WHERE icon_no = $icon_no");
  mysql_query('COMMIT'); //������ߥå�

  //DB ��³����� OutputActionResult() ��ͳ
  OutputActionResult('������������λ',
		     '�����λ����Ͽ�ڡ��������Ӥޤ���<br>'."\n" .
		     '�ڤ��ؤ��ʤ��ʤ� <a href="icon_upload.php">����</a> ��',
		     'icon_upload.php');
  break;

default:
  OutputActionResult('����������Ͽ��λ�ڡ���[���顼]', '̵���ʥ��������Ǥ���');
  break;
}
?>
