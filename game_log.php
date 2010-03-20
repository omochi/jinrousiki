<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('user_class', 'talk_class');
$INIT_CONF->LoadClass('SESSION', 'ROLES');

//-- �ǡ������� --//
$INIT_CONF->LoadRequest('RequestGameLog'); //���������
$DB_CONF->Connect(); //DB ��³
$SESSION->Certify(); //���å����ǧ��

$ROOM =& new Room($RQ_ARGS); //¼��������
$ROOM->log_mode = true;

$USERS =& new UserDataSet($RQ_ARGS); //�桼����������
$SELF = $USERS->BySession(); //��ʬ�ξ�������

if(! ($SELF->IsDead() || $ROOM->IsAfterGame())){ //��Ԥ������ཪλ�����
  OutputActionResult('�桼��ǧ�ڥ��顼',
		     '���������ĥ��顼<br>' .
		     '<a href="index.php" target="_top">�ȥåץڡ���</a>' .
		     '��������󤷤ʤ����Ƥ�������');
}
$ROOM->date      = $RQ_ARGS->date;
$ROOM->day_night = $RQ_ARGS->day_night;
$USERS->SetEvent(true);

//-- ������ --//
OutputGamePageHeader(); //HTML�إå�

echo '<table><tr><td width="1000" align="right">������ ' . $ROOM->date . ' ���� (' .
  ($ROOM->IsBeforeGame() ? '������' : ($ROOM->IsDay() ? '��' : '��')) . ')</td></tr></table>'."\n";

OutputTalkLog();       //���å�
OutputAbilityAction(); //ǽ��ȯ��
OutputLastWords();     //���
OutputDeadMan();       //��˴��
if($ROOM->IsNight()) OutputVoteList(); //��ɼ���
OutputHTMLFooter(); //HTML�եå�
