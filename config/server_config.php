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
  #var $site_root = 'http://localhost/jinrou/';
  var $site_root = 'http://localhost/jinro/';

  //�����ȥ�
  var $title = '��Ͽ�ϵ�ʤ�䡩';

  //�����ФΥ�����
  var $comment = '������λ����������';

  //�����Ф�ʸ��������
  /*
    �ѹ�����������ƤΥե����뼫�Τ�ʸ�������ɤ������ѹ����Ƥ�������
    include/init.php �⻲�Ȥ��Ƥ�������
  */
  var $encode = 'EUC-JP';

  //�����Υڡ���
  var $back_page = '';

  //�������ѥѥ����
  #var $system_password = 'xxxxxxxx';
  var $system_password = 'pass';

  //�ѥ���ɰŹ沽�� salt
  #var $salt = 'xxxx';
  var $salt = 'testtest';

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
  var $last_updated_revision = 187;

  //¼������ɽ���⡼�� (¼Ω�ƥƥ��Ȥʤɤγ�ȯ�ԥƥ����ѥ����å��Ǥ�)
  var $secret_room = false;
}

//-- ¼����ͭ�����Ф����� --//
class SharedServerConfig extends ExternalLinkBuilder{
  var $disable = true; //̵������ <ɽ���� [true:̵�� / false:ͭ��] �ˤ���>

  //ɽ������¾�Υ����ФΥꥹ��
  var $server_list = array(
    'cirno' => array('name' => '����λ�',
		     'url' => 'http://www12.atpages.jp/cirno/',
		     'encode' => 'EUC-JP',
		     'separator' => '<!-- atpages banner tag -->',
		     'footer' => '</a><br>',
		     'disable' => false),

    'eva' => array('name' => 'Eva ��',
		   'url' => 'http://jinrou.kuroienogu.net/',
		   'encode' => 'EUC-JP',
		   'separator' => '',
		   'footer' => '</a><br>',
		   'disable' => false),

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

    'sasugasister' => array('name' => 'ή����Ի�',
			    'url' => 'http://www21.atpages.jp/sasugasister/',
			    'encode' => 'UTF-8',
			    'separator' => '<!-- atpages banner tag -->',
			    'footer' => '</div></small></a><br>',
			    'disable' => false),

    'suisei' => array('name' => '�����л�',
		      'url' => 'http://alicegame.dip.jp/suisei/',
		      'encode' => 'UTF-8',
		      'separator' => '',
		      'footer' => '',
		      'disable' => false),

    'sousei' => array('name' => '�����Хƥ��Ȼ�',
		      'url' => 'http://alicegame.dip.jp/sousei/',
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
			     'disable' => false),

    'kotori' => array('name' => '��Ļ��',
		      'url' => 'http://kiterew.tv/jinro/',
		      'encode' => 'EUC-JP',
		      'separator' => '',
		      'footer' => '',
		      'disable' => false),

    /*
    'bourbon' => array('name' => '�С��ܥ�',
		       'url' => 'http://www.freedom.xii.jp/jinro/',
		       'encode' => 'EUC-JP',
		       'separator' => '',
		       'footer' => '',
		       'disable' => false),
    */
    'nekomata' => array('name' => 'ǭ����',
			'url' => 'http://jinro.blue-sky-server.com/',
			'encode' => 'UTF-8',
			'separator' => '<!-- End Ad -->',
			'footer' => '</a>',
			'disable' => false),

    'acjinrou' => array('name' => 'AC��ϵ��',
			'url' => 'http://acjinrou.blue-sky-server.com/',
			'encode' => 'EUC-JP',
			'separator' => '',
			'footer' => '',
			'disable' => false)
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
  var $password = 'xxxx'; //���������Խ��ѥ����
}

//��˥塼���ɽ������
class MenuLinkConfig extends MenuLinkConfigBase{
  var $list = array('SourceForge' => 'http://sourceforge.jp/projects/jinrousiki/',
		    '��ȯ���Х���𥹥�' =>
		    'http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1240771280/l50',
		    '������ƥ���' =>
		    'http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1246414115/l50'
		    );
  /* ���Ļ���
  var $list = array('�ܥ��� (���ΤϤ���)' =>
		    'http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1275564772/l50',
		    'Wiki' => 'http://www27.atwiki.jp/umigamejinnro/',
		    '�Ǽ���' => 'http://jbbs.livedoor.jp/netgame/2829/',
		    '����åȥ롼��' => 'http://umigamejinrou.chatx2.whocares.jp/',
		    //'�쥦�ߥ��Ứ��¼' => 'http://konoharu.sakura.ne.jp/umigame/yychat/yychat.cgi',
		    'ȿ�ʡ������ѥ���' =>
		    'http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1224519836/l50'
		    );
  */
  var $add_list = array(
    '��������' => array('����λ�' => 'http://www12.atpages.jp/cirno/',
			'Eva ��' => 'http://jinrou.kuroienogu.net/',
			'SourceForge' => 'http://sourceforge.jp/projects/jinrousiki/',
			'��ȯ���Х���𥹥�' => 'http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1240771280/l50',
			'������ƥ���' => 'http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1246414115/l50'),
    '�������ߥ����' => array('���Ļ�' => 'http://alicegame.dip.jp/sanae/',
			      'Wiki' => 'http://www27.atwiki.jp/umigamejinnro/',
			      '�Ǽ���' => 'http://jbbs.livedoor.jp/netgame/2829/',
			      '����åȥ롼��' => 'http://umigamejinrou.chatx2.whocares.jp/'),
    '�������ߥ����ͽ��' => array(//'���Ȥ껪' => 'http://satori.crz.jp/',
				  '���뻪' => 'http://www7.atpages.jp/izayoi398/'),
    '����׷�' => array('ή����Ի�' => 'http://www21.atpages.jp/sasugasister/',
			'�����л�' => 'http://alicegame.dip.jp/suisei/',
			'�����Хƥ��Ȼ�' => 'http://alicegame.dip.jp/sousei/',
			'Wiki' => 'http://www37.atwiki.jp/yaruomura/',
			'�Ǽ���' => 'http://jbbs.livedoor.jp/game/48159/',
			'ή�л��ѥġ���' => 'http://www.petitnoir.net/zinro/sasuga.html',
			'ή�����Ӹ���' => 'http://www.petitnoir.net/zinro/sasuga/yaruomura.php'),
    '����׷�ͽ��' => array('ή�з��ﻪ' => 'http://www12.atpages.jp/yaruo/jinro/',
			    'ή����Ի�' => 'http://www16.atpages.jp/sasugabros/',
			    '�鯻��廪' => 'http://www13.atpages.jp/yaranai/',
			    '��仪' => 'http://www13.atpages.jp/suigintou/',
			    '�����ݴɸ�' => 'http://www15.atpages.jp/kanaria/',
			    '��������' => 'http://www14.atpages.jp/mmr1/'),
    '��������Ŵ��' => array('�С��ܥ�ϥ�����' => 'http://bourbonhouse.xsrv.jp/jinro/',
			'΢������' => 'http://dynamis.xsrv.jp/jinro/',
			'Wiki' => 'http://www29.atwiki.jp/onmyoutetu-jinro/'),
    '��������Ŵ��ͽ��' => array('��С��ܥ�ϥ�����' => 'http://www16.atpages.jp/bourbonjinro/'),
    'iM@S��' => array('��Ļ��' => 'http://kiterew.tv/jinro/',
		      'Wiki' => 'http://www38.atwiki.jp/ijinrou/'),
    '�С��ܥ󻪷�' => array('�С��ܥ�' => 'http://www.freedom.xii.jp/jinro/',
			    'ǭ����' => 'http://jinro.blue-sky-server.com/',
			    'Wiki' => 'http://wikiwiki.jp/jinro/',
			    '�Ǽ���' => 'http://jbbs.livedoor.jp/netgame/4598/'),
    'AC ��ϵ��' => array('AC ��ϵ��' => 'http://acjinrou.blue-sky-server.com/',
			    '�Ǽ���' => 'http://acjinrou.bbs.fc2.com/'),
			);
}

//���Υ���å�ɽ������
class BBSConfig extends BBSConfigBase{
  var $disable = true; //ɽ��̵������ (true:̵���ˤ��� / false:���ʤ�)
  var $title = '���Υ���åɾ���'; //ɽ��̾
  var $raw_url = 'http://jbbs.livedoor.jp/bbs/rawmode.cgi'; //�ǡ��������� URL
  var $view_url = 'http://jbbs.livedoor.jp/bbs/read.cgi'; //ɽ���� URL
  var $thread = '/game/43883/1260623018/'; //����åɤΥ��ɥ쥹
  var $encode = 'EUC-JP'; //����åɤ�ʸ��������
  var $size = 5; //ɽ������쥹�ο�
}

//�Ǻ��������
class CopyrightConfig extends CopyrightConfigBase{
  //�����ƥ�ɸ�����
  var $list = array('�����ƥ�' =>
		    array('PHP4 + MYSQL������ץ�' => 'http://f45.aaa.livedoor.jp/~netfilms/',
			  'mbstring���ߥ�졼��' => 'http://sourceforge.jp/projects/mbemulator/'
			  ),
		    '�̿��Ǻ�' =>
		    array('ŷ�η���' => 'http://keppen.web.infoseek.co.jp/'),
		    '�ե�����Ǻ�' =>
		    array('�������ե����' => 'http://azukifont.mints.ne.jp/')
		    );

  //�ɲþ���
  var $add_list = array('�����ƥ�' =>
			array('Twitter��ƥ⥸�塼��' =>
			      'http://www.transrain.net/product/services_twitter/'),
			'�̿��Ǻ�' =>
			array('Le moineau - ������Τ���� -' => 'http://moineau.fc2web.com/'),
			'���������Ǻ�' =>
			array('�Ʒ�' => 'http://natuhotaru.yukihotaru.com/',
			      '���������Τ����Ϥ�' => 'http://jigizagi.s57.xrea.com/')
			);
}

//-- ��ȯ�ѥ��������åץ������� --//
class SourceUploadConfig{
  var $disable = false; //̵������ <���åץ��ɤ� [true:̵�� / false:ͭ��] �ˤ���>

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

//-- Twitter ������� --//
class TwitterConfig extends TwitterConfigBase{
  var $disable = true; //Twitter ���������� (true:��ߤ��� / false:���ʤ�)
  var $server = 'localhost'; //������̾
  var $hash = ''; //�ϥå��奿�� (Ǥ��)
  var $user = 'xxxx'; //�桼��̾
  var $password = 'xxxx'; //�ѥ����
}
