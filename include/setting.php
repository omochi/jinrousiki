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
  #var $site_root = 'http://localhost/jinrou/';
  var $site_root = 'http://localhost/jinro/';

  //�����ȥ�
  var $title = '��Ͽ�ϵ�ʤ�䡩';

  //�����ФΥ�����
  var $comment = '';

  //�����Ф�ʸ��������
  /*
    �ѹ�����������ƤΥե����뼫�Τ�ʸ�������ɤ������ѹ����Ƥ�������
    include/contenttyep.php �⻲�Ȥ��Ƥ�������
  */
  var $encode = 'EUC-JP';

  //�����Υڡ���
  var $back_page = '';

  //�������ѥѥ����
  var $system_password = 'xxxxxxxx';

  //�ѥ���ɰŹ沽�� salt
  var $salt = 'xxxx';

  //���������åץ��ɥե�����Υѥ����
  var $src_upload_password = 'upload';

  //�����ॾ��������Ǥ��ʤ����˻�������ñ�̤����ꤹ�뤫�ݤ�
  var $adjust_time_difference = false;

  //$adjust_time_difference ��ͭ���ʻ��λ��� (�ÿ�)
  var $offset_seconds = 32400; //9����

  //¾�ο�ϵ�����Ф�¼�����ɽ������
  var $shared_server = false;

  //ɽ������¾�Υ����ФΥꥹ��
  var $shared_server_list = array(
	'sanae' => array('name' => '���Ļ�',
			  'url' => 'http://alicegame.dip.jp/sanae/',
			  'encode' => 'UTF-8',
			  'separator' => '',
			  'footer' => ''),
	/*
	'satori' => array('name' => '���Ȥ껪',
			  'url' => 'http://satori.crz.jp/',
			  'encode' => 'EUC-JP',
			  'separator' => '',
			  'footer' => ''),
	*/
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
	/*
	'sasuga' => array('name' => 'ή�з��ﻪ',
			  'url' => 'http://www12.atpages.jp/yaruo/jinro/',
			  'encode' => 'EUC-JP',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</div></small></a><br>'),
	*/
	'sasugabros' => array('name' => 'ή����Ի�',
			  'url' => 'http://www16.atpages.jp/sasugabros/',
			  'encode' => 'UTF-8',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</div></small></a><br>'),

	'suisei' => array('name' => '�����л�',
			  'url' => 'http://alicegame.dip.jp/suisei/',
			  'encode' => 'UTF-8',
			  'separator' => '',
			  'footer' => ''),

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

	'sousei' => array('name' => '�����Хƥ��Ȼ�',
			  'url' => 'http://alicegame.dip.jp/sousei/',
			  'encode' => 'UTF-8',
			  'separator' => '',
			  'footer' => ''),
	/*
	'mohican' => array('name' => '��������',
			   'url' => 'http://www15.atpages.jp/seikima2/jinro_php/',
			   'encode' => 'UTF-8',
			   'separator' => '<!-- atpages banner tag -->',
			   'footer' => '</div></small></a><br>'),

	'mmr' => array('name' => '��������',
			'url' => 'http://www14.atpages.jp/mmr1/',
			'encode' => 'UTF-8',
			'separator' => '<!-- atpages banner tag -->',
			'footer' => '</div></small></a><br>'),
	*/
	/*
	'bourbon_test' => array('name' => '�С��ܥ�ϥ������ʲ���',
			   'url' => 'http://www16.atpages.jp/bourbonjinro/',
			   'encode' => 'UTF-8',
			   'separator' => '<!-- atpages banner tag -->',
			   'footer' => '</div></small></a><br>'),
	*/
	'bourbonhouse' => array('name' => '�С��ܥ�ϥ�����',
			   'url' => 'http://bourbonhouse.xsrv.jp/jinro/',
			   'encode' => 'EUC-JP',
			   'separator' => '',
			   'footer' => '')
				  );
}
?>
