<?php
require_once(dirname(__FILE__) . '/config.php');          //���٤�����
require_once(dirname(__FILE__) . '/version.php');         //�С���������
require_once(dirname(__FILE__) . '/contenttype_set.php'); //�إå���ʸ������������
require_once(dirname(__FILE__) . '/../paparazzi.php');    //�ǥХå���

//�����Ф�URL
$site_root = "http://localhost/jinro/";

//�����ФΥ�����
$server_comment = '���������ߥ���¼������λ���';

//�ǡ����١��������ФΥۥ���̾ hostname:port
//�ݡ����ֹ���ά����ȥǥե���ȥݡ��Ȥ����åȤ���ޤ���(MySQL:3306)
$db_host = 'localhost';

//�ǡ����١����Υ桼��̾
$db_uname = 'grayran';

//�ǡ����١��������ФΥѥ����
$db_pass = 'satorituri';

//�ǡ����١���̾
$db_name = 'jinrou';

//�������ѥѥ����
$system_password = 'pass';

//���������åץ��ɥե�����Υѥ����
$src_upload_password = 'upload';

//�����Υڡ���
$back_page = '';

//�ǥХå��⡼�ɤΥ���/����
$DEBUG_MODE = true;

//���� (�ÿ�)
$OFFSET_SECONDS = 9 * 60 * 60;
?>
