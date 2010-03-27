<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('CAST_CONF', 'GAME_OPT_CAPT');
OutputInfoPageHeader('�����४�ץ����');
?>
<p>
<a href="#wish_role"><?= $GAME_OPT_MESS->wish_role ?></a>
<a href="#real_time"><?= $GAME_OPT_MESS->real_time ?></a>
<a href="#liar"><?= $GAME_OPT_MESS->liar ?></a>
<a href="#open_vote"><?= $GAME_OPT_MESS->open_vote ?></a>
</p>
<p>
<a href="#poison"><?= $GAME_OPT_MESS->poison ?></a>
<a href="#assassin"><?= $GAME_OPT_MESS->assassin ?></a>
<a href="#boss_wolf"><?= $GAME_OPT_MESS->boss_wolf ?></a>
<a href="#poison_wolf"><?= $GAME_OPT_MESS->poison_wolf ?></a>
<a href="#possessed_wolf"><?= $GAME_OPT_MESS->possessed_wolf ?></a>
<a href="#cupid"><?= $GAME_OPT_MESS->cupid ?></a>
<a href="#medium"><?= $GAME_OPT_MESS->medium ?></a>
<a href="#mania"><?= $GAME_OPT_MESS->mania ?></a>
<a href="#decide"><?= $GAME_OPT_MESS->decide ?></a>
<a href="#authority"><?= $GAME_OPT_MESS->authority ?></a>
</p>
<p>
<a href="#gentleman"><?= $GAME_OPT_MESS->gentleman ?></a>
<a href="#sudden_death"><?= $GAME_OPT_MESS->sudden_death ?></a>
<a href="#perverseness"><?= $GAME_OPT_MESS->perverseness ?></a>
<a href="#full_mania"><?= $GAME_OPT_MESS->full_mania ?></a>
</p>
<p>
<a href="#quiz"><?= $GAME_OPT_MESS->quiz ?></a>
<a href="#duel"><?= $GAME_OPT_MESS->duel ?></a>
</p>

<h2><a name="wish_role"><?= $GAME_OPT_MESS->wish_role ?></a></h2>
<ul>
  <li><?= $GAME_OPT_CAPT->wish_role ?></li>
  <li>¼����Ͽ (�ץ쥤�䡼��Ͽ) �κݤˤʤꤿ���򿦤����򤹤뤳�Ȥ��Ǥ��ޤ�</li>
  <li>���ץ������Ȥ߹�碌�ˤ�äƴ�˾�Ǥ����򿦤ο�����ब�㤤�ޤ�</li>
</ul>

<h2><a name="real_time"><?= $GAME_OPT_MESS->real_time ?></a></h2>
<ul>
  <li><?= $GAME_OPT_CAPT->real_time ?></li>
  <li>��������̤˻���Ǥ��ޤ�</li>
</ul>

<h2><a name="open_vote"><?= $GAME_OPT_MESS->open_vote ?></a></h2>
<ul>
  <li>��ν跺��ɼ������������ޤ�</li>
  <li><?= $GAME_OPT_CAPT->open_vote ?></li>
</ul>

<h2><a name="poison"><?= $GAME_OPT_MESS->poison ?></a></h2>
<ul>
  <li>¼�ο͸���<?= $CAST_CONF->poison ?>�Ͱʾ�ˤʤä������ǼԤ��о줷�ޤ�</li>
  <li><?= $GAME_OPT_CAPT->poison ?></li>
</ul>
<h2><a name="assassin"><?= $GAME_OPT_MESS->assassin ?></a></h2>
<ul>
  <li>¼�ο͸���<?= $CAST_CONF->assassin ?>�Ͱʾ�ˤʤä���<a href="new_role/human.php#assassin">�Ż���</a>���о줷�ޤ�</li>
  <li><?= $GAME_OPT_CAPT->assassin ?></li>
</ul>
</p>
<p>
<h2><a name="boss_wolf"><?= $GAME_OPT_MESS->boss_wolf ?></a></h2>
<ul>
  <li>¼�ο͸���<?= $CAST_CONF->boss_wolf ?>�Ͱʾ�ˤʤä���<a href="new_role/wolf.php#boss_wolf">��ϵ</a>���о줷�ޤ�</li>
  <li><?= $GAME_OPT_CAPT->boss_wolf ?></li>
</ul>
</p>
<p>
<h2><a name="poison_wolf"><?= $GAME_OPT_MESS->poison_wolf ?></a></h2>
<ul>
  <li>¼�ο͸���<?= $CAST_CONF->poison_wolf ?>�Ͱʾ�ˤʤä���<a href="new_role/wolf.php#poison_wolf">��ϵ</a>���о줷�ޤ�</li>
  <li><?= $GAME_OPT_CAPT->poison_wolf ?></li>
</ul>
</p>
<p>
<h2><a name="possessed_wolf"><?= $GAME_OPT_MESS->possessed_wolf ?></a></h2>
<ul>
  <li>¼�ο͸���<?= $CAST_CONF->possessed_wolf ?>�Ͱʾ�ˤʤä���<a href="new_role/wolf.php#possessed_wolf">��ϵ</a>���о줷�ޤ�</li>
  <li><?= $GAME_OPT_CAPT->possessed_wolf ?></li>
</ul>
</p>
<p>
<h2><a name="cupid"><?= $GAME_OPT_MESS->cupid ?></a></h2>
<ul>
  <li>¼�ο͸���14�ͤ⤷����<?= $CAST_CONF->cupid ?>�Ͱʾ�ˤʤä��饭�塼�ԥåɤ��о줷�ޤ�</li>
  <li><?= $GAME_OPT_CAPT->cupid ?></li>
</ul>
</p>
<p>
<h2><a name="medium"><?= $GAME_OPT_MESS->medium ?></a></h2>
<ul>
  <li>¼�ο͸���<?= $CAST_CONF->medium ?>�Ͱʾ�ˤʤä���<a href="new_role/human.php#medium">���</a>���о줷�ޤ�</li>
  <li><?= $GAME_OPT_CAPT->medium ?></li>
</ul>
</p>
<p>
<h2><a name="mania"><?= $GAME_OPT_MESS->mania ?></a></h2>
<ul>
  <li>¼�ο͸���<?= $CAST_CONF->mania ?>�Ͱʾ�ˤʤä���<a href="new_role/human.php#mania">���åޥ˥�</a>���о줷�ޤ�</li>
  <li><?= $GAME_OPT_CAPT->mania ?></li>
</ul>
</p>
<p>
<h2><a name="decide"><?= $GAME_OPT_MESS->decide ?></a></h2>
<ul>
  <li>¼�ο͸���<?= $CAST_CONF->decide ?>�Ͱʾ�ˤʤä������Ԥ��о줷�ޤ�</li>
  <li><?= $GAME_OPT_CAPT->decide ?></li>
  <li>��ʬ������ԤǤ��뤳�ȤϤ狼��ޤ���</li>
</ul>
</p>
<p>
<h2><a name="authority"><?= $GAME_OPT_MESS->authority ?></a></h2>
<ul>
  <li>¼�ο͸���<?= $CAST_CONF->authority ?>�Ͱʾ�ˤʤä��鸢�ϼԤ��о줷�ޤ�</li>
  <li><?= $GAME_OPT_CAPT->authority ?></li>
  <li>��ʬ�����ϼԤǤ��뤳�ȤϤ狼��ޤ�</li>
</ul>

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
