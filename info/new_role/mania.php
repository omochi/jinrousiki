<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader('���򿦾��� - [���åޥ˥��ر�]', 'new_role');
?>
</head>
<body>
<h1>���åޥ˥��ر�</h1>
<p>
<a href="./" target="_top">&lt;-��˥塼</a>
<a href="summary.php">������ɽ</a>
</p>
<p>
<a href="#rule">���ܥ롼��</a>
<a href="#change_mania_group">��°�ѹ�</a>
</p>
<p>
<a href="#mania_group">���åޥ˥���</a>
</p>

<h2><a id="rule">���ܥ롼��</a></h2>
<ol>
  <li>���������ï����ͤ�����Ǥ��οͤ�Ʊ���رĤ��Ѳ������ü�ʿرĤǤ���</li>
  <li>�������ϥ��ԡ���οرĤˤʤ�ޤ���</li>
  <li>�ʤ�餫����ͳ�ǥ��ԡ�����Ω���ʤ��ä�����¼�ͿرĤȰ����ޤ���</li>
  <li>���ԡ�����Ω�������������ष������<a href="human.php#medium">���</a>��Ƚ���¼�ͿرĤǤ���</li>
</ol>

<h2><a id="change_poison_cat_group">��°�ѹ�</a></h2>
<h4>Ver. 1.4.0 ��13��</h4>
<pre>
<a href="#mania_group">���åޥ˥���</a>�ν�°��<a href="human.php">¼�Ϳر�</a>�����ѹ����ޤ�����
</pre>

<h2><a id="mania_group">���åޥ˥���</a></h2>
<p>
<a href="#mania">���åޥ˥�</a>
<a href="#trick_mania">��ѻ�</a>
<a href="#soul_mania">���ü�</a>
<a href="#unknown_mania">�</a>
<a href="#dummy_mania">̴����</a>
</p>

<h3><a id="mania">���åޥ˥�</a> (�ꤤ��̡�¼�� / ��ǽ��̡�¼��) [Ver. 1.4.0 ��11��]</h3>
<pre>
���������ï����ͤ�����Ǥ��οͤ��򿦤򥳥ԡ����ޤ���
�����ؤ��Τ�2���ܤ�ī�ǡ����åޥ˥��Ϥ����������¼�ͤˤʤ�ޤ���
�رĤ��ꤤ��̤����ƥ��ԡ�����򿦤������ؤ��ޤ���
</pre>
<h4>[�����Ԥ���Υ�����]</h4>
<pre>
�����ɿ�ϵ�ˤ����򿦤Ǥ������Ȱ㤤���ꤤ��ϵ�ʳ����򿦤⥳�ԡ����ޤ���
CO ����٤����ɤ����ϡ����ԡ������򿦼���Ǥ���
</pre>

<h3><a id="trick_mania">��ѻ�</a> (�ꤤ��̡�¼�� / ��ǽ��̡�¼��) [Ver. 1.4.0 ��9��]</h3>
<pre>
���������ï����ͤ�����Ǥ��οͤ��򿦤򥳥ԡ����ޤ���
�����ؤ��Τ�2���ܤ�ī�ǡ����åޥ˥��Ϥ����������¼�ͤˤʤ�ޤ���
�رĤ��ꤤ��̤����ƥ��ԡ�����򿦤������ؤ��ޤ���

�⤷�����ԡ��褬�����귯�ʳ��ǡ��ֽ�����ɼ�򤷤Ƥʤ��ä��׾��Ϥ����򿦤�å����ꡢ
���Ϥ��η����δ��ܿ��������ؤ�äƤ��ޤ��ޤ���
</pre>
<h4>���ԡ��η����</h4>
<pre>
1. A[��ѻ�] �� B[�����ꤤ��] =&gt; A[�����ꤤ��] B[�����ꤤ��]
��������ɼ���Ƥ���Τ������ؤ�꤬ȯ�����ޤ���
<a href="human.php#mage_group">�ꤤ�շ�</a>��<a href="human.php#mind_scanner_group">���Ȥ��</a>��<a href="fox.php#child_fox_group">�Ҹѷ�</a>��<a href="lovers.php">���Ϳر�</a>��<a href="chiroptera.php#fairy_group">������</a>�ȡ�
������<a href="wolf.php#mad_group">����</a>��<a href="fox.php#fox_group">�Ÿ�</a>������˳������ޤ�

2. A[��ѻ�] �� B[���] =&gt; A[���] B[��ǽ��]
�����ؤ�꤬ȯ�����Ƥ⥳�ԡ���ˤ��ä˥�å��������Фʤ��Τǡ�
ī��������ɽ���������ؤ�äƤ��ޤ����Ȥˤʤ�ޤ���

3. A[��ѻ�] �� B[̴���] =&gt; A[̴���] B[���]
���ξ��ϥ��ԡ���������ؤ��򼫳ФǤ��ʤ����Ȥˤʤ�ޤ���

4. A[��ѻ�] �� B[ŷ��] =&gt; A[ŷ��] B[ŷ��]
ŷ�ͤϽ�������ɼ���ޤ��󤬡���˴����������Τ��㳰Ū�������ؤ��оݳ��Ǥ���

5. A[��ѻ�] �� B[���ϵ] �� �����귯 =&gt; A[���ϵ] B[���ϵ]
��ɼ���Ƥ���ϵ�򥳥ԡ��������������ؤ���ȯ�����ޤ���

6. A[��ѻ�] �� B[櫻�] =&gt; A[櫻�] B[����]
��������ɼ���Ƥ��ʤ����ͤ������ؤ���ȯ�����ޤ�
</pre>
<h4>[�����Ԥ���Υ�����]</h4>
<pre>
�ֽ�°�رĤϽ����������ɼ�ǳ��ꤹ��פȤ����롼����ϰ����
������ǽ�Ϥ�å�����򿦤���ʤ����ʡ��ȻװƤ��Ƥ������������ˤʤ�ޤ�����
</pre>

<h3><a id="soul_mania">���ü�</a> (�ꤤ��̡�¼�� / ��ǽ��̡�¼��) [Ver. 1.4.0 ��11��]</h3>
<pre>
���������ï����ͤ�����Ǥ��οͤ��򿦤ξ�̼���Ѳ������ü��<a href="#mania">���åޥ˥�</a>��
</pre>
<ol>
<li>�����ؤ��Τ�4���ܤ�ī�ǡ�����ޤǤϳ��üԤΤޤޤǤ�</li>
<li>2���ܤ�ī�ˤɤ��򿦷Ϥˤʤ�Τ� (���ԡ�����򿦤η���) ʬ����ޤ�<br>
��) A[���ü�] �� B[��ϵ]  =&gt; ��B����Ͽ�ϵ�Ǥ�����
</li>
<li>4���ܤ�ī�ˤɤ��򿦤ˤʤä��Τ�ʬ����ޤ���</li>
<li>���åޥ˥��Ϥ����������¼�ͤˤʤ�ޤ���</li>
<li>��������륱����������Τǡ���˴���Ƥ��Ƥ������ؤ������ϹԤʤ��ޤ�</li>
</ol>
<pre>
���ԡ����η��� �� ���ԡ����
</pre>
<ol>
<li><a href="human.php#human_group">¼�ͷ�</a> �� <a href="human.php#executor">���Լ�</a></li>
<li><a href="human.php#mage_group">�ꤤ�շ�</a> �� <a href="human.php#soul_mage">�����ꤤ��</a></li>
<li><a href="human.php#necromancer_group">��ǽ�Է�</a> �� <a href="human.php#soul_necromancer">������</a></li>
<li><a href="human.php#medium_group">�����</a> �� <a href="human.php#revive_medium">����</a> (Ver. 1.4.0 ��13��)</li>
<li><a href="human.php#priest_group">�ʺ׷�</a> �� <a href="human.php#bishop_priest">�ʶ�</a></li>
<li><a href="human.php#guard_group">��ͷ�</a> �� <a href="human.php#poison_guard">����</a></li>
<li><a href="human.php#common_group">��ͭ�Է�</a> �� <a href="human.php#ghost_common">˴���</a></li>
<li><a href="human.php#poison_group">���ǼԷ�</a> �� <a href="human.php#strong_poison">���Ǽ�</a></li>
<li><a href="human.php#poison_cat_group">ǭ����</a> �� <a href="human.php#revive_cat">��ì</a></li>
<li><a href="human.php#pharmacist_group">���շ�</a> �� <a href="human.php#pharmacist">����</a></li>
<li><a href="human.php#assassin_group">�Ż��Է�</a> �� <a href="human.php#soul_assassin">�Ի¤�</a> (Ver. 1.4.0 ��13��)</li>
<li><a href="human.php#mind_scanner_group">���Ȥ��</a> �� <a href="human.php#howl_scanner">������</a></li>
<li><a href="human.php#jealousy_group">��ɱ��</a> �� <a href="human.php#poison_jealousy">�Ƕ�ɱ</a></li>
<li><a href="human.php#doll_group">�峤�ͷ���</a> �� <a href="human.php#doll_master">�ͷ�����</a></li>
<li><a href="wolf.php#wolf_group">��ϵ��</a> �� <a href="wolf.php#sirius_wolf">ŷϵ</a></li>
<li><a href="wolf.php#mad_group">���ͷ�</a> �� <a href="wolf.php#whisper_mad">�񤭶���</a></li>
<li><a href="fox.php#fox_group">�Ÿѷ�</a> �� <a href="fox.php#cursed_fox">ŷ��</a></li>
<li><a href="fox.php#child_fox_group">�Ҹѷ�</a> �� <a href="fox.php#jammer_fox">���</a> (Ver. 1.4.0 ��14��)</li>
<li><a href="lovers.php#cupid_group">���塼�ԥåɷ�</a> �� <a href="lovers.php#mind_cupid">����</a></li>
<li><a href="lovers.php#angel_group">ŷ�ȷ�</a> �� <a href="lovers.php#ark_angel">��ŷ��</a></li>
<li><a href="quiz.php#quiz_group">����Է�</a> �� <a href="quiz.php#quiz">�����</a></li>
<li><a href="vampire.php#vampire_group">�۷쵴��</a> �� <a href="vampire.php#vampire">�۷쵴</a> (Ver. 1.4.0 ��14��)</li>
<li><a href="chiroptera.php#chiroptera_group">������</a> �� <a href="chiroptera.php#boss_chiroptera">������</a></li>
<li><a href="chiroptera.php#fairy_group">������</a> �� <a href="chiroptera.php#light_fairy">������</a></li>
<li><a href="mania.php#mania_group">���åޥ˥���</a> �� ¼��</li>
</ol>
<h4>[�����Ԥ���Υ�����]</h4>
<pre>
<a href="human.php#incubate_poison">���Ǽ�</a>�ΰ�������Ȥ��ƿ������ۤ����ä���̡��������������ˤʤ�ޤ�����
ǽ��ȯư�Υ����ߥ󥰤��θ����<a href="human.php#incubate_poison">���Ǽ�</a>�������᤯�����ؤ�������ԤäƤ��ޤ���
</pre>

<h3><a id="unknown_mania">�</a> (�ꤤ��̡�¼�� / ��ǽ��̡�¼��) [Ver. 1.4.0 ��23��]</h3>
<pre>
���������ï����ͤ�����Ǥ��οͤ�Ʊ����°�رĤˤʤ�ޤ���
��̤�ɽ�������Τ� 2 ���ܤ�ī�ǡ���ʬ����ɼ���<a href="sub_role.php#mind_friend">���ļ�</a>���Ĥ��ޤ���
�����ؤ��Τ�2���ܤ�ī�Ǥ���
��¸������ȤϾ��¼�ͤʤΤǡ��¼��Ͻ�°�ر������ζ��������Ǥ���

<a href="#mania">���åޥ˥�</a>�Ȱ㤤�����ԡ���̤��Фʤ��Τǥ��ԡ����ʹ���ʤ���
��ʬ�ν�°�رĤ�ʬ����ޤ���
</pre>
<h4>��°�رĤ�Ƚ����</h4>
<pre>
1. � �� ¼�� (¼�Ϳر�)
������ͭ�ԤȤʤ�ޤ�

2. � �� ��ϵ (��ϵ�ر�)
��ɼ��Ȥ������äǤ���<a href="wolf.php#whisper_mad">�񤭶���</a>�����Ǥ���

3. � �� �Ÿ� (�Ÿѿر�)
��°���ŸѤǤ������Ȥ��Ÿѥ�����Ȥ���ʤ��Τǵ���Ĥ��ޤ��礦��

4. � �� ���塼�ԥå� (���Ϳر�)
��ʬ�����ͤ�����ʤ����塼�ԥå������ˤʤ�ޤ���

5. � �� ���� (�����ر�)
��ɼ��Ȳ��äǤ������������ˤʤ�ޤ���
��������ȼ�ʬ�ξ��Ԥ�̵�ط��Ǥ���

6. � �� ��ϵ[����] (��ϵ�ر�)
�����򿦤�Ƚ���оݳ�(<a href="human.php#medium">���</a>��Ʊ��)�ʤΤ�
���ԡ���Ⱦ����رĤ��ۤʤ롢�㳰�������Ȥʤ�ޤ���

7. � �� ��ϵ[���ȥ��] (��ϵ�ر�)
���ԡ��褬¼�ͿرĤ�<a href="human.php#mind_scanner">���Ȥ�</a>�˲��ä�������Ƥ�����֤ʤΤ�
���ԡ��褫��ξ������꤬�񤷤��ʤ�ޤ���

8. � �� �۷쵴 (�۷쵴�ر�)
�۷쵴�رĤξ������λ��;塢�����Ф˾��Ƥʤ����ˤʤ�ޤ���
���äơ��۷쵴����ľ�˼�ʬ�����Τ�𤲤ʤ����������Ȼפ��ޤ���

9. � �� � �� ��ϵ (������ϵ�ر�)
���ԡ��褬���ä������ʳ����򿦤�������ޤ�
���ԡ����é�ä�Ƚ�ꤷ�ޤ���

10. �A �� �B �� �C �� �A (����¼�Ϳر�)
���ԡ����é�äƼ�ʬ����ä�����¼�ͿرĤˤʤ�ޤ���

11. � �� ���åޥ˥� �� �Ÿ� (�Ÿѿر�)
���åޥ˥��򥳥ԡ��������ϥ��ԡ���̤οرĤˤʤ�ޤ���

12. �A �� ���åޥ˥� �� �B �� ��ϵ
���åޥ˥����򥳥ԡ�������¼�ͤˤʤ�Τ��Υ�󥯤��ڤ�ޤ���
��̤Ȥ��ưʲ��Τ褦�ˤʤ�ޤ���
�A(¼�Ϳر�) �� ¼��(�����åޥ˥�)���B (��ϵ�ر�) �� ��ϵ
</pre>
<h4>[�����Ԥ���Υ�����]</h4>
<pre>
����׿�ϵ���� GM �ˡ��ֽ鿴�Ԥλ����Ѥ˻Ȥ����򿦡פ�
��������Ƥ������������ˤ��Ƥߤޤ�����
󬤬�鿴�Ԥ򥳥ԡ����ƻ���륤�᡼���Ǥ��͡�

�⤷�⡢�����Ƥ�餦���˥��ԡ��褬���Ǥ��ޤä��鼫ʬ�ν�°�رĤ�
�����������פˤʤ���ˤʤ�ޤ����ȤäƤ����ԿԤǤ��͡�
</pre>

<h3><a id="dummy_mania">̴����</a> (�ꤤ��̡�¼�� / ��ǽ��̡�¼��) [Ver. 1.4.0 ��11��]</h3>
<pre>
���������ï����ͤ�����Ǥ��οͤ��򿦤δ��ܡ���������Ѳ�����<a href="#mania">���åޥ˥�</a>�ΰ��
�ܿͤ�ɽ���ϡ�<a href="#soul_mania">���ü�</a>�פǡ����ͤ�Ʊ����
�Ѳ�����<a href="wolf.php#dream_eater_mad">��</a>�˽��⤵���Ȼ�����롣
</pre>
<pre>
���ԡ����η��� �� ���ԡ����
</pre>
<ol>
<li><a href="human.php#human_group">¼�ͷ�</a> �� <a href="human.php#suspect">�Կ���</a></li>
<li><a href="human.php#mage_group">�ꤤ�շ�</a> �� <a href="human.php#dummy_mage">̴����</a></li>
<li><a href="human.php#necromancer_group">��ǽ�Է�</a> �� <a href="human.php#dummy_necromancer">̴���</a></li>
<li><a href="human.php#medium_group">�����</a> �� <a href="human.php#medium">���</a></li>
<li><a href="human.php#priest_group">�ʺ׷�</a> �� <a href="human.php#crisis_priest">�¸���</a></li>
<li><a href="human.php#guard_group">��ͷ�</a> �� <a href="human.php#dummy_guard">̴���</a></li>
<li><a href="human.php#common_group">��ͭ�Է�</a> �� <a href="human.php#dummy_common">̴��ͭ��</a></li>
<li><a href="human.php#poison_group">���ǼԷ�</a> �� <a href="human.php#dummy_poison">̴�Ǽ�</a></li>
<li><a href="human.php#poison_cat_group">ǭ����</a> �� <a href="human.php#sacrifice_cat">ǭ��</a></li>
<li><a href="human.php#pharmacist_group">���շ�</a> �� <a href="human.php#cure_pharmacist">��Ƹ</a></li>
<li><a href="human.php#assassin_group">�Ż��Է�</a> �� <a href="human.php#eclipse_assassin">���Ż���</a></li>
<li><a href="human.php#mind_scanner_group">���Ȥ��</a> �� <a href="human.php#mind_scanner">���Ȥ�</a></li>
<li><a href="human.php#jealousy_group">��ɱ��</a> �� <a href="human.php#jealousy">��ɱ</a></li>
<li><a href="human.php#doll_group">�峤�ͷ���</a> �� <a href="human.php#doll">�峤�ͷ�</a></li>
<li><a href="wolf.php#wolf_group">��ϵ��</a> �� <a href="wolf.php#cute_wolf">˨ϵ</a></li>
<li><a href="wolf.php#mad_group">���ͷ�</a> �� <a href="wolf.php#mad">����</a></li>
<li><a href="fox.php#fox_group">�Ÿѷ�</a> �� <a href="fox.php#cute_fox">˨��</a></li>
<li><a href="fox.php#child_fox_group">�Ҹѷ�</a> �� <a href="fox.php#sex_fox">����</a></li>
<li><a href="lovers.php#cupid_group">���塼�ԥåɷ�</a> �� <a href="lovers.php#self_cupid">�ᰦ��</a></li>
<li><a href="lovers.php#angel_group">ŷ�ȷ�</a> �� <a href="lovers.php#angel">ŷ��</a></li>
<li><a href="quiz.php#quiz_group">����Է�</a> �� <a href="quiz.php#quiz">�����</a></li>
<li><a href="vampire.php#vampire_group">�۷쵴��</a> �� <a href="vampire.php#vampire">�۷쵴</a> (Ver. 1.4.0 ��14��)</li>
<li><a href="chiroptera.php#chiroptera_group">������</a> �� <a href="chiroptera.php#dummy_chiroptera">̴�ᰦ��</a></li>
<li><a href="chiroptera.php#fairy_group">������</a> �� <a href="chiroptera.php#mirror_fairy">������</a></li>
<li><a href="mania.php#mania_group">���åޥ˥���</a> �� ¼��</li>
</ol>
<h4>[�����Ԥ���Υ�����]</h4>
<pre>
<a href="#soul_mania">���ü�</a>��̴�С������Ǥ���
�ǽ�Ū�ˤϼ��Ф��뤳�Ȥ��Ǥ���Τ�¾��̴�Ϥ���٤���б��Ϥ��䤹�����⤷��ޤ���
</pre>
</body></html>
