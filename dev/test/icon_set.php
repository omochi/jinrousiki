<?php
exit;
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');

if(! $DEBUG_MODE){
  OutputActionResult('ǧ�ڥ��顼', '���Υ�����ץȤϻ��ѤǤ��ʤ�����ˤʤäƤ��ޤ���');
}
$INIT_CONF->LoadClass('ICON_CONF');

$DB_CONF->Connect(); //DB ��³
//mysql_query("UPDATE user_icon SET category = '�������' WHERE icon_no > 0 AND icon_no <= 10");
//mysql_query("UPDATE user_icon SET appearance = '�����Ͱ���' WHERE icon_no = 11");
//mysql_query("UPDATE user_icon SET category = '����Project' WHERE icon_no >= 12 AND icon_no < 78");
//mysql_query("UPDATE user_icon SET appearance = '�����š�̴' WHERE icon_no >= 23 AND icon_no <= 34");
//mysql_query("UPDATE user_icon SET appearance = '������̴��' WHERE icon_no = 35");
//mysql_query("UPDATE user_icon SET appearance = '�������뾶' WHERE icon_no >= 36 AND icon_no <= 43");
//mysql_query("UPDATE user_icon SET appearance = '�����ֱ���' WHERE icon_no >= 44 AND icon_no <= 48");
//mysql_query("UPDATE user_icon SET appearance = '��������Ͽ' WHERE icon_no >= 49 AND icon_no <= 56");
//mysql_query("UPDATE user_icon SET appearance = '��������ŷ' WHERE icon_no >= 57 AND icon_no <= 58");
//mysql_query("UPDATE user_icon SET appearance = '����������' WHERE icon_no >= 59 AND icon_no <= 66");
//mysql_query("UPDATE user_icon SET appearance = '��������Ʋ' WHERE icon_no >= 67 AND icon_no <= 68");
//mysql_query("UPDATE user_icon SET appearance = '����������' WHERE icon_no >= 69 AND icon_no <= 71");
//mysql_query("UPDATE user_icon SET appearance = '������ʹ�˵�' WHERE icon_no = 72");
//mysql_query("UPDATE user_icon SET appearance = '����ѳ�' WHERE icon_no >= 73 AND icon_no <= 75");
//mysql_query("UPDATE user_icon SET appearance = '���������' WHERE icon_no >= 76 AND icon_no <= 77");
//mysql_query("UPDATE user_icon SET appearance = '�ȥ�󥹥ե����ޡ�G1' WHERE icon_no = 78");
//mysql_query("UPDATE user_icon SET category = '�ȥ�󥹥ե����ޡ�' WHERE icon_no = 78");
//mysql_query("UPDATE user_icon SET appearance = '�ͥȥ��¶���2ch�Ǽ���' WHERE icon_no = 79 OR icon_no = 90");
//mysql_query("UPDATE user_icon SET appearance = '��Ŵ������' WHERE icon_no = 80");
//mysql_query("UPDATE user_icon SET appearance = '�ݥ��åȥ�󥹥��� �⡦��' WHERE icon_no = 81");
//mysql_query("UPDATE user_icon SET category = '�ݥ��åȥ�󥹥���' WHERE icon_no = 81");
//mysql_query("UPDATE user_icon SET appearance = '�Ϥ���ʹ֥��㡼�ȥ륺' WHERE icon_no = 82");
//mysql_query("UPDATE user_icon SET appearance = '�狼���' WHERE icon_no = 83");
//mysql_query("UPDATE user_icon SET category = '����Project' WHERE icon_no >= 87 AND icon_no <= 89");
//mysql_query("UPDATE user_icon SET appearance = '������ϡ��' WHERE icon_no >= 87 AND icon_no <= 89");
//mysql_query("UPDATE user_icon SET category = '����Project' WHERE icon_no >= 91 AND icon_no <= 96");
//mysql_query("UPDATE user_icon SET appearance = '������' WHERE icon_no = 91");
//mysql_query("UPDATE user_icon SET appearance = '������ϡ��' WHERE icon_no >= 92 AND icon_no <= 96");
//mysql_query('COMMIT'); //������ߥå�

//DB ��³����� OutputActionResult() ��ͳ
OutputActionResult('������λ', '������λ��');
