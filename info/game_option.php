<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('CAST_CONF', 'GAME_OPT_CAPT');
OutputInfoPageHeader('�����४�ץ����');
?>
<p>
<a href="#wish_role"><?php echo $GAME_OPT_MESS->wish_role ?></a>
<a href="#real_time"><?php echo $GAME_OPT_MESS->real_time ?></a>
<a href="#open_vote"><?php echo $GAME_OPT_MESS->open_vote ?></a>
<a href="#open_day"><?php echo $GAME_OPT_MESS->open_day ?></a>
</p>
<p>
<a href="#dummy_boy"><?php echo $GAME_OPT_MESS->dummy_boy ?></a>
<a href="#gm_login"><?php echo $GAME_OPT_MESS->gm_login ?></a>
<a href="#gerd"><?php echo $GAME_OPT_MESS->gerd ?></a>
</p>
<p>
<a href="#not_open_cast"><?php echo $GAME_OPT_MESS->not_open_cast ?></a>
<a href="#auto_open_cast"><?php echo $GAME_OPT_MESS->auto_open_cast ?></a>
</p>
<p>
<a href="#poison"><?php echo $GAME_OPT_MESS->poison ?></a>
<a href="#assassin"><?php echo $GAME_OPT_MESS->assassin ?></a>
<a href="#boss_wolf"><?php echo $GAME_OPT_MESS->boss_wolf ?></a>
<a href="#poison_wolf"><?php echo $GAME_OPT_MESS->poison_wolf ?></a>
<a href="#possessed_wolf"><?php echo $GAME_OPT_MESS->possessed_wolf ?></a>
<a href="#sirius_wolf"><?php echo $GAME_OPT_MESS->sirius_wolf ?></a>
<a href="#cupid"><?php echo $GAME_OPT_MESS->cupid ?></a>
<a href="#medium"><?php echo $GAME_OPT_MESS->medium ?></a>
<a href="#mania"><?php echo $GAME_OPT_MESS->mania ?></a>
<a href="#decide"><?php echo $GAME_OPT_MESS->decide ?></a>
<a href="#authority"><?php echo $GAME_OPT_MESS->authority ?></a>
</p>
<p>
<a href="#liar"><?php echo $GAME_OPT_MESS->liar ?></a>
<a href="#gentleman"><?php echo $GAME_OPT_MESS->gentleman ?></a>
<a href="#sudden_death"><?php echo $GAME_OPT_MESS->sudden_death ?></a>
<a href="#perverseness"><?php echo $GAME_OPT_MESS->perverseness ?></a>
<a href="#detective"><?php echo $GAME_OPT_MESS->detective ?></a>
<a href="#festival"><?php echo $GAME_OPT_MESS->festival ?></a>
</p>
<p>
<a href="#replace_human"><?php echo $GAME_OPT_MESS->replace_human ?></a>
<a href="#full_mania"><?php echo $GAME_OPT_MESS->full_mania ?></a>
<a href="#full_chiroptera"><?php echo $GAME_OPT_MESS->full_chiroptera ?></a>
<a href="#full_cupid"><?php echo $GAME_OPT_MESS->full_cupid ?></a>
</p>
<p>
<a href="#quiz"><?php echo $GAME_OPT_MESS->quiz ?></a>
<a href="#duel"><?php echo $GAME_OPT_MESS->duel ?></a>
</p>

<h2><a id="wish_role"><?php echo $GAME_OPT_MESS->wish_role ?></a></h2>
<ul>
  <li><?php echo $GAME_OPT_CAPT->wish_role ?></li>
  <li>¼����Ͽ (�ץ쥤�䡼��Ͽ) �κݤˤʤꤿ���򿦤����򤹤뤳�Ȥ��Ǥ��ޤ�</li>
  <li>���ץ������Ȥ߹�碌�ˤ�äƴ�˾�Ǥ����򿦤ο�����ब�㤤�ޤ�</li>
</ul>

<h2><a id="real_time"><?php echo $GAME_OPT_MESS->real_time ?></a></h2>
<ul>
  <li><?php echo $GAME_OPT_CAPT->real_time ?></li>
  <li>��������̤˻���Ǥ��ޤ�</li>
</ul>

<h2><a id="open_vote"><?php echo $GAME_OPT_MESS->open_vote ?></a></h2>
<ul>
  <li>��ν跺��ɼ������������ޤ�</li>
  <li><?php echo $GAME_OPT_CAPT->open_vote ?></li>
</ul>

<h2><a id="open_day"><?php echo $GAME_OPT_MESS->open_day ?></a> [Ver. 1.4.0 ��12��]</h2>
<ul>
  <li><?php echo $GAME_OPT_CAPT->open_day ?></li>
  <li>��ʬ���򿦤�ʬ����ޤ���1���������ɼ�Ǥ��ޤ���</li>
  <li>���»��֤�᤮���鼫ư������ڤ��ؤ��ޤ� (�̾���Υ�����������)</li>
</ul>

<h2><a id="dummy_boy"><?php echo $GAME_OPT_MESS->dummy_boy ?></a></h2>
<ul>
  <li>�������롢�����귯��ϵ�˿��٤��ޤ�</li>
  <li>�����귯���ʤ���򿦤ˤ����¤�����ޤ�</li>
  <li>�����귯�ϡ�����Ū�ˤ�ǽ�Ϥ�ȯư���ޤ���</li>
</ul>

<h2><a id="gm_login"><?php echo $GAME_OPT_MESS->gm_login ?></a> [Ver. 1.4.0 ��18��]</h2>
<ul>
  <li>���� GM �������귯�Ȥ��ƥ����󤷤ޤ�</li>
  <li>¼��Ω�Ƥ�ݤ˥�����ѥ���ɤ����Ϥ��ޤ�</li>
  <li>�����귯�Υ桼��̾�ϡ�dummy_boy�פǤ�</li>
</ul>

<h2><a id="gerd"><?php echo $GAME_OPT_MESS->gerd ?></a> [Ver. 1.4.0 ��12��]</h2>
<ul>
  <li><?php echo $GAME_OPT_CAPT->gerd ?></li>
  <li>����⡼�ɤθ��������¼�ͤ����ɲä��ޤ�</li>
  <li>���åޥ˥����ץ�����դ��Ƥ��Ƥ�¼�ͤ��ͳ��ݤ��ޤ�</li>
  <li>��Ʈ¼�����פ�¼������������ؤ��ޤ��� (�ǽ餫��¸�ߤ�����Τ�ͭ���Ǥ�)</li>
</ul>

<h2><a id="not_open_cast"><?php echo $GAME_OPT_MESS->not_open_cast ?></a></h2>
<ul>
  <li>ï���ɤ��򿦤ʤΤ�����������ޤ���</li>
  <li>����ǽ�Ϥ�ͭ���ˤʤ�ޤ�</li>
</ul>

<h2><a id="auto_open_cast"><?php echo $GAME_OPT_MESS->auto_open_cast ?></a> [Ver. 1.4.0 ��3��]</h2>
<ul>
  <li>����ǽ�ϼԤʤɤ�ǽ�Ϥ���äƤ���֤������������ˤʤ�ޤ�</li>
</ul>

<h2><a id="poison"><?php echo $GAME_OPT_MESS->poison ?></a></h2>
<ul>
  <li>¼�ο͸���<?php echo $CAST_CONF->poison ?>�Ͱʾ�ˤʤä������ǼԤ��о줷�ޤ�</li>
  <li><?php echo $GAME_OPT_CAPT->poison ?></li>
</ul>
<h2><a id="assassin"><?php echo $GAME_OPT_MESS->assassin ?></a> [Ver. 1.4.0 ��4��]</h2>
<ul>
  <li>¼�ο͸���<?php echo $CAST_CONF->assassin ?>�Ͱʾ�ˤʤä���<a href="new_role/human.php#assassin">�Ż���</a>���о줷�ޤ�</li>
  <li><?php echo $GAME_OPT_CAPT->assassin ?></li>
</ul>
</p>
<p>
<h2><a id="boss_wolf"><?php echo $GAME_OPT_MESS->boss_wolf ?></a> [Ver. 1.4.0 ��3-7��]</h2>
<ul>
  <li>¼�ο͸���<?php echo $CAST_CONF->boss_wolf ?>�Ͱʾ�ˤʤä���<a href="new_role/wolf.php#boss_wolf">��ϵ</a>���о줷�ޤ�</li>
  <li><?php echo $GAME_OPT_CAPT->boss_wolf ?></li>
</ul>
</p>
<p>
<h2><a id="poison_wolf"><?php echo $GAME_OPT_MESS->poison_wolf ?></a> [Ver. 1.4.0 ��14��]</h2>
<ul>
  <li>¼�ο͸���<?php echo $CAST_CONF->poison_wolf ?>�Ͱʾ�ˤʤä���<a href="new_role/wolf.php#poison_wolf">��ϵ</a>���о줷�ޤ�</li>
  <li><?php echo $GAME_OPT_CAPT->poison_wolf ?></li>
</ul>
</p>
<p>
<h2><a id="possessed_wolf"><?php echo $GAME_OPT_MESS->possessed_wolf ?></a> [Ver. 1.4.0 ��4��]</h2>
<ul>
  <li>¼�ο͸���<?php echo $CAST_CONF->possessed_wolf ?>�Ͱʾ�ˤʤä���<a href="new_role/wolf.php#possessed_wolf">��ϵ</a>���о줷�ޤ�</li>
  <li><?php echo $GAME_OPT_CAPT->possessed_wolf ?></li>
</ul>
</p>
<h2><a id="sirius_wolf"><?php echo $GAME_OPT_MESS->sirius_wolf ?></a> [Ver. 1.4.0 ��9��]</h2>
<ul>
  <li>¼�ο͸���<?php echo $CAST_CONF->sirius_wolf ?>�Ͱʾ�ˤʤä���<a href="new_role/wolf.php#sirius_wolf">ŷϵ</a>���о줷�ޤ�</li>
  <li><?php echo $GAME_OPT_CAPT->sirius_wolf ?></li>
</ul>
</p>
<p>
<h2><a id="cupid"><?php echo $GAME_OPT_MESS->cupid ?></a> [Ver. 1.2.0��]</h2>
<ul>
  <li>¼�ο͸���14�ͤ⤷����<?php echo $CAST_CONF->cupid ?>�Ͱʾ�ˤʤä��饭�塼�ԥåɤ��о줷�ޤ�</li>
  <li><?php echo $GAME_OPT_CAPT->cupid ?></li>
</ul>
</p>
<p>
<h2><a id="medium"><?php echo $GAME_OPT_MESS->medium ?></a> [Ver. 1.4.0 ��14��]</h2>
<ul>
  <li>¼�ο͸���<?php echo $CAST_CONF->medium ?>�Ͱʾ�ˤʤä���<a href="new_role/human.php#medium">���</a>���о줷�ޤ�</li>
  <li><?php echo $GAME_OPT_CAPT->medium ?></li>
</ul>
</p>
<p>
<h2><a id="mania"><?php echo $GAME_OPT_MESS->mania ?></a> [Ver. 1.4.0 ��14��]</h2>
<ul>
  <li>¼�ο͸���<?php echo $CAST_CONF->mania ?>�Ͱʾ�ˤʤä���<a href="new_role/human.php#mania">���åޥ˥�</a>���о줷�ޤ�</li>
  <li><?php echo $GAME_OPT_CAPT->mania ?></li>
</ul>
</p>
<p>
<h2><a id="decide"><?php echo $GAME_OPT_MESS->decide ?></a></h2>
<ul>
  <li>¼�ο͸���<?php echo $CAST_CONF->decide ?>�Ͱʾ�ˤʤä������Ԥ��о줷�ޤ�</li>
  <li><?php echo $GAME_OPT_CAPT->decide ?></li>
  <li>��ʬ������ԤǤ��뤳�ȤϤ狼��ޤ���</li>
</ul>
</p>
<p>
<h2><a id="authority"><?php echo $GAME_OPT_MESS->authority ?></a></h2>
<ul>
  <li>¼�ο͸���<?php echo $CAST_CONF->authority ?>�Ͱʾ�ˤʤä��鸢�ϼԤ��о줷�ޤ�</li>
  <li><?php echo $GAME_OPT_CAPT->authority ?></li>
  <li>��ʬ�����ϼԤǤ��뤳�ȤϤ狼��ޤ�</li>
</ul>

<h2><a id="liar"><?php echo $GAME_OPT_MESS->liar ?></a> [Ver. 1.4.0 ��14��]</h2>
<ul>
  <li>���桼���˰���γ�Ψ (70% ����) ��<a href="new_role/sub_role.php#liar">ϵ��ǯ</a>���Ĥ��ޤ�</li>
</ul>

<h2><a id="gentleman"><?php echo $GAME_OPT_MESS->gentleman ?></a> [Ver. 1.4.0 ��14��]</h2>
<ul>
  <li>���桼������Ͽ�������̤˱�����<a href="new_role/sub_role.php#gentleman">�»�</a>��<a href="new_role/sub_role.php#gentleman">�ʽ�</a>���Ĥ��ޤ�</li>
  <li>����⡼�ɤǥ�������ղä������ϸġ������̤򻲾Ȥ��Ƥ��ޤ���</li>
  <li>ȯưΨ�ϥ������ղäξ���Ʊ���Ǥ�</li>
</ul>

<h2><a id="sudden_death"><?php echo $GAME_OPT_MESS->sudden_death ?></a> [Ver. 1.4.0 ��14��]</h2>
<ul>
  <li>���桼����<a href="new_role/sub_role.php#chicken_group">�����Է�</a>�Τɤ줫���Ĥ��ޤ�</li>
  <li><a href="new_role/sub_role.php#impatience">û��</a>���Ĥ��ΤϺ���ǰ�ͤǤ�</li>
  <li><a href="new_role/sub_role.php#panelist">������</a>�ϤĤ��ޤ��� (�Ĥ�����Х��Ǥ�)</li>
  <li><a href="#perverseness"><?php echo $GAME_OPT_MESS->perverseness ?></a>��ʻ�ѤǤ��ޤ���</li>
</ul>

<h2><a id="perverseness"><?php echo $GAME_OPT_MESS->perverseness ?></a> [Ver. 1.4.0 ��19��]</h2>
<ul>
  <li>���桼����<a href="new_role/sub_role.php#perverseness">ŷ�μٵ�</a>���Ĥ��ޤ�</li>
  <li><a href="#sudden_death"><?php echo $GAME_OPT_MESS->sudden_death ?></a>��ʻ�ѤǤ��ޤ���</li>
</ul>

<h2><a id="detective"><?php echo $GAME_OPT_MESS->detective ?></a> [Ver. 1.4.0 ��10��]</h2>
<ul>
  <li><?php echo $GAME_OPT_CAPT->detective ?></li>
  <li>����¼�ξ��ϡ���ͭ�Ԥ�����ж�ͭ�Ԥ򡢤��ʤ����¼�ͤ���<a href="new_role/human.php#detective_common">õ��</a>�������ؤ��ޤ�</li>
  <li>��������⡼�ɤξ��ϸ����Ȥ�<a href="new_role/human.php#detective_common">õ��</a>���ɲä���ޤ�</li>
  <li>���Υ��ץ�������Ѥ������ϡ������귯��<a href="new_role/human.php#detective_common">õ��</a>�ˤϤʤ�ޤ���</li>
  <li>�ֿ����귯��GM��+�������������ץ��ץ�����ʻ�Ѥ���ȡ��õ��⡼�ɡפˤʤ�ޤ�</li>
  <li>���õ��⡼�ɡפϥ����೫��ľ���õ�夬��˴���ơ���˰�ư���ޤ����ؼ��� GM ��ͳ�ǹԤ��ޤ�</li>
</ul>

<h2><a id="festival"><?php echo $GAME_OPT_MESS->festival ?></a> [Ver. 1.4.0 ��9��]</h2>
<ul>
  <li>�����ͤ��������ह���ü�����Ǥ�</li>
  <li>�������Ǥϡ��ʲ��˼����Ϳ����ϰϤ��������������ˤʤ�ޤ�</li>
</ul>
<pre>
 8�͡�¼��1���ꤤ��1����ǽ��1�����1����ϵ1������1�����1������1
 9�͡����2��̴���4����ϵ1����ϵ1��ŷ��1
10�͡�¼��2��ƨ˴��1���ꤤ��1����ǽ��1�����1����ϵ2������1���Ÿ�1
11�͡�̵�ռ�1�������ꤤ��1��������1���¸���1�����1�����1����Ƹ1����ϵ1����ϵ1������1��������1
12�͡���ϵ1������8������2������1
13�͡�¼��4���ꤤ��1����ǽ��1�����1���峤�ͷ�1���ͷ�����1����ϵ2��������1������1
14�͡���ǽ1����ϵ2���Ÿ�1������10
15�͡����Ǽ�3����ϵ3��������1���Ÿ�1������6��������1
16�͡�̴���1�����Ǽ�1��̴�Ǽ�5��ŷϵ3����1��������1��������4
17�͡��Ҥ褳�����1����ǽ��1�����1����ͭ��2����ϵ2����ϵ1������1���Ÿ�1������7
18�͡�����1�������ꤤ��1��������1��Ǧ��1������1��˴���1�����Ǽ�1��ȿ����1����ϵ1����ϵ1��ŷϵ1������1�����ѻ�1������1�����1��ŷ��1��������1����ѻ�1
19�͡�ŷ��1�����1��̴�Ǽ�1��ǭ��1�����Ż���2����ɱ1����ϵ1����ϵ1��ŷϵ1��������1����ư��1��ŷ��2������1�������1��������1��������1��������1
20�͡���ϵ1����ϵ1����ϵ2�����ѻ�2�����1�����1�����1������5��������1������5
21�͡����Ǽ�7��Ϣ�Ǽ�2����ϵ4������ϵ1���ɸ�2�������3��������2
22�͡�¼��8���ꤤ��1����ǽ��1�����1����ͭ��2��ǭ��1����ϵ4����ϵ1������1���Ÿ�1���Ҹ�1

��Ÿ��
10�͡�ƨ˴��¼������
13�͡�����¼����������
15�͡��ޥ��󥹥�����¼����������
22�͡��Х륵��¼���狼��ƻ�
</pre>

<h2><a id="replace_human"><?php echo $GAME_OPT_MESS->replace_human ?></a> [Ver. 1.4.0 ��14��]</h2>
<ul>
  <li><?php echo $GAME_OPT_CAPT->replace_human ?></li>
  <li><?php echo $GAME_OPT_MESS->full_mania ?>���ĥ���Ƽ����������ץ����Ǥ�</li>
  <li>ɽ����¼�ͤȤʤ��򿦤�¸�ߤ��������դ��Ƥ�������</li>
  <li>��<?php echo $GAME_OPT_MESS->replace_human ?>�פϴ����ͤ��������ह�뤳�Ȥ�����ˤ������ץ����Ǥ�<br>
    ���ߤν�����������<a href="new_role/human.php#escaper">ƨ˴��</a>�ˤʤ�ޤ�
  </li>
</ul>
<h3><a id="full_mania"><?php echo $GAME_OPT_MESS->full_mania ?></a> [Ver. 1.4.0 ��17��]</h3>
<ul>
  <li>¼�ͤ�����<a href="new_role/human.php#mania">���åޥ˥�</a>�ˤʤ�ޤ�</li>
</ul>
<h3><a id="full_chiroptera"><?php echo $GAME_OPT_MESS->full_chiroptera ?></a> [Ver. 1.4.0 ��14��]</h3>
<ul>
  <li>¼�ͤ�����<a href="new_role/chiroptera.php#chiroptera">����</a>�ˤʤ�ޤ�</li>
</ul>
<h3><a id="full_cupid"><?php echo $GAME_OPT_MESS->full_cupid ?></a> [Ver. 1.4.0 ��14��]</h3>
<ul>
  <li>¼�ͤ�����<a href="new_role/lovers.php#cupid">���塼�ԥå�</a>�ˤʤ�ޤ�</li>
</ul>



<h2><a id="quiz"><?php echo $GAME_OPT_MESS->quiz ?></a> [Ver. 1.4.0 ��2��]</h2>
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

<h2><a id="duel"><?php echo $GAME_OPT_MESS->duel ?></a> [Ver. 1.4.0 ��19��]</h2>
<ul>
  <li><?php echo $GAME_OPT_CAPT->duel ?></li>
  <li>����������������ʤ��ץ��ץ���������ˤ�ä������Ѥ��ޤ����������ϰʲ��Ǥ�</li>
  <ol>
    <li>����������Ż��ԥ١���</li>
    <li>��ư���������塼�ԥåɥ١���</li>
    <li>����������Ǽԥ١���</li>
  </ol>
</ul>
</body></html>
