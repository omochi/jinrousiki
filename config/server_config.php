<?php
//-- �ǡ����١������� --//
class DatabaseConfig extends DatabaseConfigBase{
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

//-- ���������� --//
class ServerConfig{
  //�����Ф�URL
  var $site_root = 'http://localhost/jinrou/';

  //�����ȥ�
  var $title = '��Ͽ�ϵ�ʤ�䡩';

  //�����ФΥ�����
  var $comment = '';

  //�����Ф�ʸ��������
  /*
    �ѹ�����������ƤΥե����뼫�Τ�ʸ�������ɤ������ѹ����Ƥ�������
    include/init.php �⻲�Ȥ��Ƥ�������
  */
  var $encode = 'EUC-JP';

  //�����Υڡ���
  var $back_page = '';

  //�������ѥѥ����
  var $system_password = 'xxxxxxxx';

  //�ѥ���ɰŹ沽�� salt
  var $salt = 'xxxx';

  //�����ॾ��������Ǥ��ʤ����˻�������ñ�̤����ꤹ�뤫�ݤ�
  var $adjust_time_difference = false;

  //$adjust_time_difference ��ͭ���ʻ��λ��� (�ÿ�)
  var $offset_seconds = 32400; //9����

  //�������Υ�����ץȤΥ�ӥ�����ֹ�
  /*
    �� ���ε�ǽ�� Ver. 1.4.0 beta1 (revision 152) �Ǽ�������ޤ�����

    �������Υ�����ץȤ� class ScriptInfo (config/version.php) ��
    �������Ƥ��� $revision �����ꤹ�뤳�Ȥ� admin/setup.php ��
    �Ԥ����������Ŭ������ޤ���

    ������������ץȤ����֤�����䡢�ǡ����١�������ٴ����õ��
    �����֤������ 0 �����ꤷ�Ʋ�������

    �������Υ�����ץȤ˳����ե�������ѿ����ʤ����䡢
    �С������ʬ����ʤ����� 1 �����ꤷ�Ƥ���������

    ������Υ�ӥ�����ֹ��Ʊ�������������礭���ͤ����ꤹ���
    admin/setup.php �ν����Ͼ�������åפ���ޤ���
  */
  var $last_updated_revision = 0;
}

//-- ¼����ͭ�����Ф����� --//
class SharedServerConfig{
  var $disable = true; //̵������ <ɽ���� [true:̵�� / false:ͭ��] �ˤ���>

  //ɽ������¾�Υ����ФΥꥹ��
  var $server_list = array(
    'sanae' => array('name' => '���Ļ�',
		     'url' => 'http://alicegame.dip.jp/sanae/',
		     'encode' => 'UTF-8',
		     'separator' => '',
		     'footer' => '',
		     'disable' => false),
    /*
    'satori' => array('name' => '���Ȥ껪',
		      'url' => 'http://satori.crz.jp/',
		      'encode' => 'EUC-JP',
		      'separator' => '',
		      'footer' => '',
		      'disable' => true),
    */
    'sakuya' => array('name' => '���뻪',
		      'url' => 'http://www7.atpages.jp/izayoi398/',
		      'encode' => 'EUC-JP',
		      'separator' => '<!-- atpages banner tag -->',
		      'footer' => '</div></small></a><br>',
		      'disable' => false),

    'cirno' => array('name' => '����λ�',
		     'url' => 'http://www12.atpages.jp/cirno/',
		     'encode' => 'EUC-JP',
		     'separator' => '<!-- atpages banner tag -->',
		     'footer' => '</a><br>',
		     'disable' => false),
    /*
    'sasuga' => array('name' => 'ή�з��ﻪ',
		      'url' => 'http://www12.atpages.jp/yaruo/jinro/',
		      'encode' => 'EUC-JP',
		      'separator' => '<!-- atpages banner tag -->',
		      'footer' => '</div></small></a><br>',
		      'disable' => true),
    */
    'sasugabros' => array('name' => 'ή����Ի�',
			  'url' => 'http://www16.atpages.jp/sasugabros/',
			  'encode' => 'UTF-8',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</div></small></a><br>',
			  'disable' => true),

    'suisei' => array('name' => '�����л�',
		      'url' => 'http://alicegame.dip.jp/suisei/',
		      'encode' => 'UTF-8',
		      'separator' => '',
		      'footer' => '',
		      'disable' => false),

    'bara' => array('name' => '�鯻��廪',
		    'url' => 'http://www13.atpages.jp/yaranai/',
		    'encode' => 'UTF-8',
		    'separator' => '<!-- atpages banner tag -->',
		    'footer' => '</a><br>',
		    'disable' => false),

    'suigin' => array('name' => '��仪',
		      'url' => 'http://www13.atpages.jp/suigintou/',
		      'encode' => 'UTF-8',
		      'separator' => '<!-- atpages banner tag -->',
		      'footer' => '</a><br>',
		      'disable' => false),

    'sousei' => array('name' => '�����Хƥ��Ȼ�',
		      'url' => 'http://alicegame.dip.jp/sousei/',
		      'encode' => 'UTF-8',
		      'separator' => '',
		      'footer' => '',
		      'disable' => false),

    'mohican' => array('name' => '�������ƥ��Ȼ�',
		       'url' => 'http://www15.atpages.jp/seikima2/jinro_php/',
		       'encode' => 'UTF-8',
		       'separator' => '<!-- atpages banner tag -->',
		       'footer' => '</div></small></a><br>',
		       'disable' => true),

    'mmr' => array('name' => '��������',
		   'url' => 'http://www14.atpages.jp/mmr1/',
		   'encode' => 'UTF-8',
		   'separator' => '<!-- atpages banner tag -->',
		   'footer' => '</div></small></a><br>',
		   'disable' => true),

    'bourbon_test' => array('name' => '�С��ܥ�ϥ������ʲ���',
			    'url' => 'http://www16.atpages.jp/bourbonjinro/',
			    'encode' => 'UTF-8',
			    'separator' => '<!-- atpages banner tag -->',
			    'footer' => '</div></small></a><br>',
			    'disable' => true),

    'bourbonhouse' => array('name' => '�С��ܥ�ϥ�����',
			    'url' => 'http://bourbonhouse.xsrv.jp/jinro/',
			    'encode' => 'EUC-JP',
			    'separator' => '',
			    'footer' => '',
			    'disable' => false),

    'bourbon_chaos' => array('name' => '΢������',
			     'url' => 'http://dynamis.xsrv.jp/jinro/',
			     'encode' => 'EUC-JP',
			     'separator' => '',
			     'footer' => '',
			     'disable' => true),

    'kotori' => array('name' => '��Ļ��',
		      'url' => 'http://kiterew.tv/jinro/',
		      'encode' => 'EUC-JP',
		      'separator' => '',
		      'footer' => '',
		      'disable' => true)
			   );
}

//����������Ͽ����
class UserIcon extends UserIconBase{
  var $disable_upload = false; //true; //��������Υ��åץ��ɤ�������� (true:��ߤ��� / false:���ʤ�)
  var $name   = 30;    //��������̾�ˤĤ�����ʸ����(Ⱦ��)
  var $size   = 15360; //���åץ��ɤǤ��륢������ե�����κ�������(ñ�̡��Х���)
  var $width  = 45;    //���åץ��ɤǤ��륢������κ�����
  var $height = 45;    //���åץ��ɤǤ��륢������κ���⤵
  var $number = 1000;  //��Ͽ�Ǥ��륢������κ����
  var $password = 'xxxxxxxx'; //���������Խ��ѥ����
}

//-- ��ȯ�ѥ��������åץ������� --//
class SourceUploadConfig{
  var $disable = true; //̵������ <���åץ��ɤ� [true:̵�� / false:ͭ��] �ˤ���>

  //���������åץ��ɥե�����Υѥ����
  var $password = 'upload';

  //�ե�����κ���ʸ������ɽ��̾
  var $form_list = array('name'     => array('size' => 20, 'label' => '�ե�����̾'),
			 'caption'  => array('size' => 80, 'label' => '�ե����������'),
			 'user'     => array('size' => 20, 'label' => '������̾'),
			 'password' => array('size' => 20, 'label' => '�ѥ����'));

  //����ե����륵���� (�Х���)
  var $max_size = 10485760; //10 Mbyte
}
