<?php
//�ǡ����١�������
class DatabaseConfig{
  //�ǡ����١��������ФΥۥ���̾ hostname:port
  //�ݡ����ֹ���ά����ȥǥե���ȥݡ��Ȥ����åȤ���ޤ���(MySQL:3306)
  var $host = 'localhost';

  //�ǡ����١����Υ桼��̾
  #var $user = 'xxxxxx';
  var $user = 'grayran';

  //�ǡ����١��������ФΥѥ����
  #var $password = 'xxxxxx';
  var $password = 'satorituri';

  //�ǡ����١���̾
  var $name = 'jinrou';
}
$DB_CONF = new DatabaseConfig();

//����������
class ServerConfig{
  //�����Ф�URL
  var $site_root = 'http://localhost/jinro/';

  //�����ȥ�
  var $title = '��Ͽ�ϵ�ʤ�䡩';

  //�����ФΥ�����
  var $server_comment = '���������ߥ���¼������λ���';

  //�����Υڡ���
  var $back_page = '';

  //�������ѥѥ����
  var $system_password = 'xxxxxx';

  //���������åץ��ɥե�����Υѥ����
  var $src_upload_password = 'upload';

  //���� (�ÿ�)
  var $offset_seconds = 32400; //9����

  //¾�ο�ϵ�����Ф�¼�����ɽ������
  var $shared_server = false;
}
$SERVER_CONF = new ServerConfig();

//�ǥХå��⡼�ɤΥ���/����
$DEBUG_MODE = false;

//�����ե�������ɤ߹���
require_once(dirname(__FILE__) . '/config.php');          //���٤�����
require_once(dirname(__FILE__) . '/version.php');         //�С���������
require_once(dirname(__FILE__) . '/contenttype_set.php'); //�إå���ʸ������������
require_once(dirname(__FILE__) . '/../paparazzi.php');    //�ǥХå���
?>
