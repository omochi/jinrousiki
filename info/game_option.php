<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('GAME_OPT_CAPT');
OutputInfoPageHeader('�����४�ץ����');
?>
<p>
<a href="#liar"><?= $GAME_OPT_MESS->liar ?></a>
<a href="#gentleman"><?= $GAME_OPT_MESS->gentleman ?></a>
<a href="#sudden_death"><?= $GAME_OPT_MESS->sudden_death ?></a>
<a href="#perverseness"><?= $GAME_OPT_MESS->perverseness ?></a>
<a href="#full_mania"><?= $GAME_OPT_MESS->full_mania ?></a>
<a href="#quiz"><?= $GAME_OPT_MESS->quiz ?></a>
<a href="#duel"><?= $GAME_OPT_MESS->duel ?></a>
</p>

<h2><a name="liar"><?= $GAME_OPT_MESS->liar ?></a></h2>
<ul>
  <li>���桼���˰���γ�Ψ (70% ����) ��<a href="new_role/sub_role.php#liar">ϵ��ǯ</a>���Ĥ��ޤ�</li>
</ul>

<h2><a name="gentleman"><?= $GAME_OPT_MESS->gentleman ?></a></h2>
<ul>
  <li>���桼������Ͽ�������̤˱�����<a href="new_role/sub_role.php#gentleman">�»�</a>��<a href="new_role/sub_role.php#gentleman">�ʽ�</a>���Ĥ��ޤ�</li>
  <li>����⡼�ɤǥ�������ղä������ϸġ������̤򻲾Ȥ��Ƥ��ޤ���</li>
  <li>ȯưΨ�ϥ������ղäξ���Ʊ���Ǥ�</li>
</ul>

<h2><a name="sudden_death"><?= $GAME_OPT_MESS->sudden_death ?></a></h2>
<ul>
  <li>���桼����<a href="new_role/sub_role.php#chicken_group">�����Է�</a>�Τɤ줫���Ĥ��ޤ�</li>
  <li><a href="new_role/sub_role.php#impatience">û��</a>���Ĥ��ΤϺ���ǰ�ͤǤ�</li>
  <li><a href="new_role/sub_role.php#panelist">������</a>�ϤĤ��ޤ��� (�Ĥ�����Х��Ǥ�)</li>
  <li><a href="#perverseness"><?= $GAME_OPT_MESS->perverseness ?></a>��ʻ�ѤǤ��ޤ���</li>
</ul>

<h2><a name="perverseness"><?= $GAME_OPT_MESS->perverseness ?></a></h2>
<ul>
  <li>���桼����<a href="new_role/sub_role.php#perverseness">ŷ�μٵ�</a>���Ĥ��ޤ�</li>
  <li><a href="#sudden_death"><?= $GAME_OPT_MESS->sudden_death ?></a>��ʻ�ѤǤ��ޤ���</li>
</ul>

<h2><a name="full_mania"><?= $GAME_OPT_MESS->full_mania ?></a></h2>
<ul>
  <li>¼�ͤ�����<a href="new_role/human.php#mania">���åޥ˥�</a>�ˤʤ�ޤ�</li>
  <li>ɽ����¼�ͤȤʤ��򿦤�¸�ߤ��������դ��Ƥ�������</li>
</ul>

<h2><a name="quiz"><?= $GAME_OPT_MESS->quiz ?></a></h2>
<ul>
  <li>GM ��<a href="new_role/quiz.php#quiz">�����</a>�ˤʤ�ޤ�</li>
  <li>GM �⥲���೫����ɼ�򤹤�ɬ�פ�����ޤ�</li>
  <li>�и��򿦤�¼�͡���ͭ�ԡ���ϵ�����͡��ŸѤǤ�</li>
  <li>GM �ʳ���������<a href="new_role/sub_role.php#panelist">������</a>���Ĥ��ޤ�</li>
  <li>��ϵ�Ͼ�� GM ���������ޤ���</li>
  <li>GM �ϳ��ޤ�Ƥ⻦����ޤ���</li>
  <li>�ʲ��Τ褦�ʻȤ��������ꤷ�Ƥ��ޤ�</li>
  <ol>
    <li>GM ������������ꤷ�ƥ����೫��</li>
    <li>��ϵ��Ŭ���ʥ����ߥ󥰤� GM �����</li>
    <li>�뤬��������桼������������</li>
    <li>�������������� GM ������ȯɽ</li>
    <li>�桼���ϴְ�äƤ����� GM ����ɼ������ʤ� GM �ʳ�����ɼ</li>
    <li>GM ������Ԥ���ǰ��ֲ������٤��ä��ͤ���ɼ</li>
    <li>GM �������������˼����������ꤹ��</li>
    <li>�ʲ������Ԥ���ޤ�ޤǷ����֤�</li>
  </ol>
</ul>

<h2><a name="duel"><?= $GAME_OPT_MESS->duel ?></a></h2>
<ul>
  <li><a href="new_role/human.php#assassin">�Ż���</a>����ϵ��<a href="new_role/wolf.php#trap_mad">櫻�</a>�����и����ޤ���
</ul>
</body></html>
