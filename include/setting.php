<?php
//�ǡ����١�������
class DatabaseConfig{
  //�ǡ����١��������ФΥۥ���̾ hostname:port
  //�ݡ����ֹ���ά����ȥǥե���ȥݡ��Ȥ����åȤ���ޤ���(MySQL:3306)
  var $host = 'localhost';

  //�ǡ����١����Υ桼��̾
  #var $user = 'xxxx';
  var $user = 'grayran';

  //�ǡ����١��������ФΥѥ����
  #var $password = 'xxxxxxxx';
  var $password = 'satorituri';

  //�ǡ����١���̾
  var $name = 'jinrou';
}

//����������
class ServerConfig{
  //�����Ф�URL
  #var $site_root = 'http://localhost/jinro/';
  var $site_root = 'http://localhost/jinrou/';

  //�����ȥ�
  var $title = '��Ͽ�ϵ�ʤ�䡩';

  //�����ФΥ�����
  var $comment = '';

  //�����Υڡ���
  var $back_page = '';

  //�������ѥѥ����
  var $system_password = 'xxxxxxxx';

  //�ѥ���ɰŹ沽�� salt
  var $salt = 'xxxx';

  //���������åץ��ɥե�����Υѥ����
  var $src_upload_password = 'upload';

  //�����ॾ��������Ǥ��ʤ����˻�������ñ�̤����ꤹ�뤫�ݤ�
  var $adjust_time_difference = true;
  
  //$adjust_time_difference��ͭ���ʻ��λ��� (�ÿ�)
  var $offset_seconds = 32400; //9����

  //¾�ο�ϵ�����Ф�¼�����ɽ������
  var $shared_server = false;

  // GM���¶������뤫�ݤ�(��������¨�����ϡ������ब���Ѳ�ǽ)
  var $power_gm = false;

  //ɽ������¾�Υ����ФΥꥹ��
  var $shared_server_list = array(
	'sanae' => array('name' => '���Ļ�',
			  'url' => 'http://alicegame.dip.jp/sanae/',
			  'encode' => 'UTF-8'),

	'satori' => array('name' => '���Ȥ껪',
			  'url' => 'http://satori.crz.jp/',
			  'encode' => 'EUC-JP'),

	'sakuya' => array('name' => '���뻪',
			  'url' => 'http://www7.atpages.jp/izayoi398/',
			  'encode' => 'EUC-JP',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</div></small></a><br>'),

	'cirno' => array('name' => '����λ�',
			 'url' => 'http://www12.atpages.jp/cirno/',
			  'encode' => 'EUC-JP',
			 'separator' => '<!-- atpages banner tag -->',
			 'footer' => '</a><br>'),

	'sasuga' => array('name' => 'ή�з��ﻪ',
			  'url' => 'http://www12.atpages.jp/yaruo/jinro/',
			  'encode' => 'EUC-JP',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</div></small></a><br>'),

	'suise' => array('name' => '�����л�',
			  'url' => 'http://alicegame.dip.jp/suisei/',
			  'encode' => 'UTF-8'),

	'bara' => array('name' => '�鯻��廪',
			'url' => 'http://www13.atpages.jp/yaranai/',
			'encode' => 'UTF-8',
			'separator' => '<!-- atpages banner tag -->',
			'footer' => '</a><br>'),

	'suigin' => array('name' => '��仪',
			  'url' => 'http://www13.atpages.jp/suigintou/',
			  'encode' => 'UTF-8',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</a><br>'),

	'mohican' => array('name' => '��������',
			   'url' => 'http://www12.atpages.jp/yagio/jinro_php_files/jinro_php/',
			   'encode' => 'EUC-JP',
			   'separator' => '<!-- atpages banner tag -->',
			   'footer' => '</div></small></a><br>')
				  );
}
?>
