<?php
exit;
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');

if(! $DEBUG_MODE){
  OutputActionResult('ǧ�ڥ��顼', '���Υ�����ץȤϻ��ѤǤ��ʤ�����ˤʤäƤ��ޤ���');
}
$INIT_CONF->LoadClass('ICON_CONF');

$DB_CONF->Connect(); //DB ��³
//UpdateIconInfo('category', '�������', 1, 10);
//UpdateIconInfo('category', '����Project', 11, 78);
//UpdateIconInfo('appearance', '�������⶿', 13, 21);
//UpdateIconInfo('appearance', '�����š�̴', 22, 33);
//UpdateIconInfo('appearance', '������̴��', 34);
//UpdateIconInfo('appearance', '�������뾶', 35, 42);
//UpdateIconInfo('appearance', '�����ֱ���', 43, 47);
//UpdateIconInfo('appearance', '��������Ͽ', 48, 55);
//UpdateIconInfo('appearance', '��������ŷ', 56, 57);
//UpdateIconInfo('appearance', '����������', 58, 65);
//UpdateIconInfo('appearance', '��������Ʋ', 66, 67);
//UpdateIconInfo('appearance', '����������', 68, 70);
//UpdateIconInfo('appearance', '������ʹ�˵�', 71);
//UpdateIconInfo('appearance', '����ѳ�', 72);
//UpdateIconInfo('appearance', '���������', 76, 77);
//UpdateIconInfo('appearance', '�����Ͱ���', 91, 92);
//UpdateIconInfo('appearance', '����̴����', 181);
//UpdateIconInfo('appearance', '���������', 185, 186);
//UpdateIconInfo('appearance', '������', 121);
//UpdateIconInfo('category', '������', 121);
//UpdateIconInfo('category', '�ݥ��åȥ�󥹥���', 96, 97);
//UpdateIconInfo('appearance', '�ݥ��åȥ�󥹥��� �⡦��', 96);
//UpdateIconInfo('appearance', '�Ϥ���ʹ֥��㡼�ȥ륺', 99);
//UpdateIconInfo('appearance', '�ȥ�󥹥ե����ޡ�G1', 106);
//UpdateIconInfo('category', '�ȥ�󥹥ե����ޡ�', 106);
//UpdateIconInfo('appearance', 'Rozen Maiden', 118);
//UpdateIconInfo('category', '������ᥤ�ǥ�', 118);
//UpdateIconInfo('appearance', '�餭������', 144);
//UpdateIconInfo('author', '�Ʒ�', 12, 77);
//UpdateIconInfo('author', '���������Τ����Ϥ�', 109, 111);
mysql_query('COMMIT'); //������ߥå�

//DB ��³����� OutputActionResult() ��ͳ
OutputActionResult('������λ', '������λ��');

//-- �ؿ� --//
function UpdateIconInfo($type, $value, $from, $to = NULL){
  $query = isset($to) ? "{$from} <= icon_no AND icon_no <= {$to}" : "icon_no = {$from}";
  mysql_query("UPDATE user_icon SET {$type} = '{$value}' WHERE {$query}");
}
