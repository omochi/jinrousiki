<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('GAME_OPT_CAPT');
OutputInfoPageHeader('����⡼��');
?>
<p>
<a href="#wish_role"><?php echo $GAME_OPT_MESS->wish_role ?></a>
<a href="#chaos_decide_role">�������롼����</a>
</p>
<p>
<a href="#chaos"><?php echo $GAME_OPT_MESS->chaos ?></a>
<a href="#chaosfull"><?php echo $GAME_OPT_MESS->chaosfull ?></a>
<a href="#chaos_hyper"><?php echo $GAME_OPT_MESS->chaos_hyper ?></a>
<a href="#chaos_old"><?php echo $GAME_OPT_MESS->chaos ?> (������)</a>
<a href="#chaos_open_cast"><?php echo $GAME_OPT_MESS->chaos_open_cast ?></a>
<a href="#sub_role_limit"><?php echo $GAME_OPT_MESS->sub_role_limit ?></a>
</p>

<h2><a id="wish_role"><?php echo $GAME_OPT_MESS->wish_role ?></a></h2>
<pre>
�������˷��Ƥ��顢�и������򿦥��롼�פ��˾���Ƥ����
ͥ��Ū�����򤵤����ͤǤ���

��1) �ꤤ�դ��˾�����ꤤ�ա������ꤤ�դ��и�����
�� �ꤤ�դ������ꤤ�դΤɤ��餫�ˤʤ�ޤ�

��2) �Ż��Ԥ��˾�������и����ʤ��ä�
�� ��˾�ʤ���Ʊ�������ˤʤ�ޤ�
</pre>

<h3>Ver. 1.4.0 ��24��</h3>
<pre>
��˾�����ץ����ͭ���ˤʤ�ޤ���
</pre>

<h3>Ver. 1.4.0 ��11��</h3>
<pre>
��˾�����ץ����϶���Ū�˥��դˤʤ�ޤ�
</pre>

<h2><a id="chaos_decide_role">�������롼����</a></h2>
<ol>
  <li>�С�����󥢥åפǻ��ͤ��Ѥ���ǽ��������ޤ���</li>
  <li>�����೫��ľ��˾��Ԥ���ޤ��ǽ��������ޤ�</li>
  <li>����ե�������ѹ��Ǥ���ΤǶ���Ū�ʿ��ͤϥ�������˰㤤�ޤ���</li>
</ol>
<p>
<a href="#chaosfull_decide_role_fix">����и���</a>
<a href="#chaosfull_decide_role_random">������и���</a>
<a href="#chaosfull_decide_role_example">���������</a>
</p>

<h3><a id="chaosfull_decide_role_fix">����и���</a></h3>
<pre>
�������Ͽ�ϵ1���ꤤ��1�ǡ�������٤Ǹ��̤�����Ǥ��ޤ���
�������������귯���ꤤ�դˤʤ��ǽ���⤢��Τ� CO �����ꤤ�դ����Ǥ���Ȥϸ¤�ޤ���
</pre>

<h3><a id="chaosfull_decide_role_random">������и���</a></h3>
<ol>
  <li>���򿦤νи�Ψ�ϴ���Ū�ˤ�������Ǥ���</li>

  <li>�������򿦥��롼�פˤϺ���и��������ꤷ�Ƥ��ޤ���<br>
    ��) ��ϵ�Ͽ͸��� 1/10 �����³�����Ƥ�
  </li>

  <li>�򿦥��롼����˿͸����Ф����¤����ꤵ��Ƥ��ޤ���<br>
    ��) ��ϵ�� 20%���ꤤ�դ� 10%
  </li>

  <li>������и����򿦥��롼�פξ�¤�Ķ�����¼�ͤ˿����֤��ޤ���</li>

  <li>¼�ͤˤϿ͸����Ф����¤����ꤵ��Ƥ��ޤ���<br>
    ��¤�Ķ�����������򿦤˿����֤��ޤ�(�������Ͽ��åޥ˥�)��
  </li>
</ol>

<h3><a id="chaos_decide_role_example">���������</a></h3>
<ul>
  <li>¼�͡�10��</li>

  <li>���򿦤ξ���� (�ºݤȤϿ������㤤�ޤ�)</li>
  <ul>
    <li>�ꤤ�շϡ�20%</li>
    <li>��ǽ�Էϡ�10%</li>
    <li>��ϵ�ϡ�20%</li>
  </ul>
</ul>

<pre>
1. ������ (�ºݤϥ�������˰㤤�ޤ�)
�ꤤ��1����ǽ��1����ϵ1

2. �����������ä��������
�ꤤ��2�����������1����ǽ��1��������1����ϵ1����ϵ1��˨ϵ3

3. �������
3-1. ����¿���Ȥ�����ͥ��Ū�˺���ޤ���
�ʤ�٤����ब¿���Ф�褦�ˤ��뤿��Ǥ���

�ꤤ�շ�3 �� 2
�ꤤ��1�����������1��¼��1

3-2. �����Ȥ������оݤˤʤ�ޤ���
��ǽ�Է�2 �� 1
��ǽ��1��¼��1

3-3. Ʊ�����Ǥɤ��餫����ʤ��Ȥ����ʤ����ϥ�����Ǥ�
(�򿦤νи�Ψ�Ȥϴط�����ޤ���)
��ϵ��5�� 2

��ϵ1 �ϸ���ʤΤ������оݳ����Ĥޤꡢ˨ϵ3����ϵ1����3����ޤ�
����¿���ۤ�������Τǡ�˨ϵ-2�ϳ��ꡣ
�Ǹ��˨ϵ1����ϵ1���������Ǥɤ��餫������ޤ���

���������
��ϵ1����ϵ1��¼��3
��������������
¼��5���ꤤ��1�����������1����ǽ��1����ϵ1����ϵ1

4. ¼�;������
�ֿ��åޥ˥�¼�פΥ��ץ�����դ��Ƥ��ʤ�����
¼�ͤξ�¤�Ķ��������åޥ˥��˿����֤��ޤ���

¼�ͤξ���㡡�͸���10%
¼��5 �� ¼��1�����åޥ˥�4

5. �ǽ�����
¼��1���ꤤ��1�����������1����ǽ��1����ϵ1����ϵ1�����åޥ˥�4
</pre>

<h2><a id="chaos"><?php echo $GAME_OPT_MESS->chaos ?></a></h2>
<h3><a id="chaos_appear_role">�и���</a></h3>
<pre>
�и������ǽ���Τ����򿦤ϰʲ��Ǥ���
</pre>
<h4><a href="new_role/human.php">¼�Ϳر�</a></h4>
<pre>
<a href="new_role/human.php#human_group">¼�ͷ�</a>��¼�͡�<a href="new_role/human.php#escaper">ƨ˴��</a>
<a href="new_role/human.php#mage_group">�ꤤ�շ�</a>���ꤤ�ա�<a href="new_role/human.php#soul_mage">�����ꤤ��</a>��<a href="new_role/human.php#psycho_mage">���������</a>
<a href="new_role/human.php#necromancer_group">��ǽ�Է�</a>����ǽ�ԡ�<a href="new_role/human.php#medium">���</a>
<a href="new_role/human.php#guard_group">��ͷ�</a>����͡�<a href="new_role/human.php#poison_guard">����</a>��<a href="new_role/human.php#reporter">�֥�</a>
<a href="new_role/human.php#common_group">��ͭ�Է�</a>����ͭ��
<a href="new_role/human.php#poison_group">���ǼԷ�</a>�����Ǽԡ�<a href="new_role/human.php#incubate_poison">���Ǽ�</a>
<a href="new_role/human.php#pharmacist_group">���շ�</a>��<a href="new_role/human.php#pharmacist">����</a>
<a href="new_role/human.php#assassin_group">�Ż��Է�</a>��<a href="new_role/human.php#assassin">�Ż���</a>
<a href="new_role/human.php#doll_group">�峤�ͷ���</a>��<a href="new_role/human.php#doll">�峤�ͷ�</a>��<a href="new_role/human.php#doll_master">�ͷ�����</a>
<a href="new_role/human.php#mania_group">���åޥ˥���</a>��<a href="new_role/human.php#mania">���åޥ˥�</a>
</pre>

<h4><a href="new_role/wolf.php">��ϵ�ر�</a></h4>
<pre>
<a href="new_role/wolf.php#wolf_group">��ϵ��</a>����ϵ��<a href="new_role/wolf.php#boss_wolf">��ϵ</a>��<a href="new_role/wolf.php#poison_wolf">��ϵ</a>��<a href="new_role/wolf.php#tongue_wolf">���ϵ</a>��<a href="new_role/wolf.php#silver_wolf">��ϵ</a>
<a href="new_role/wolf.php#mad_group">���ͷ�</a>�����͡�<a href="new_role/wolf.php#fanatic_mad">������</a>��<a href="new_role/wolf.php#whisper_mad">�񤭶���</a>
</pre>

<h4><a href="new_role/fox.php">�Ÿѿر�</a></h4>
<pre>
<a href="new_role/fox.php#fox_group">�Ÿѷ�</a>���Ÿ�
<a href="new_role/fox.php#child_fox_group">�Ҹѷ�</a>��<a href="new_role/fox.php#child_fox">�Ҹ�</a>
</pre>

<h4><a href="new_role/lovers.php">���Ϳر�</a></h4>
<pre>
<a href="new_role/lovers.php#cupid_group">���塼�ԥåɷ�</a>�����塼�ԥåɡ�<a href="new_role/lovers.php#self_cupid">�ᰦ��</a>
</pre>

<h4><a href="new_role/quiz.php">����Կر�</a></h4>
<pre>
<a href="new_role/quiz.php#quiz_group">����Է�</a>��<a href="new_role/quiz.php#quiz">�����</a>
</pre>

<h4><a href="new_role/chiroptera.php">�����ر�</a></h4>
<pre>
<a href="new_role/chiroptera.php#chiroptera_group">������</a>��<a href="new_role/chiroptera.php#chiroptera">����</a>
</pre>

<h2><a id="chaosfull"><?php echo $GAME_OPT_MESS->chaosfull ?></a></h2>
<h3><a id="chaosfull_appear_role">�и���</a></h3>
<pre>
�и������ǽ���Τ����򿦤ϰʲ� (Ver. 1.4.0 ��23 ����) �Ǥ���
</pre>
<h4><a href="new_role/human.php">¼�Ϳر�</a></h4>
<pre>
<a href="new_role/human.php#human_group">¼�ͷ�</a>��¼�͡�<a href="new_role/human.php#suspect">�Կ���</a>��<a href="new_role/human.php#unconscious">̵�ռ�</a>
<a href="new_role/human.php#mage_group">�ꤤ�շ�</a>���ꤤ�ա�<a href="new_role/human.php#soul_mage">�����ꤤ��</a>��<a href="new_role/human.php#psycho_mage">���������</a>��<a href="new_role/human.php#sex_mage">�Ҥ褳�����</a>��<a href="new_role/human.php#voodoo_killer">���ۻ�</a>��<a href="new_role/human.php#dummy_mage">̴����</a>
<a href="new_role/human.php#necromancer_group">��ǽ�Է�</a>����ǽ�ԡ�<a href="new_role/human.php#soul_necromancer">������</a>��<a href="new_role/human.php#yama_necromancer">����</a>��<a href="new_role/human.php#dummy_necromancer">̴���</a>��<a href="new_role/human.php#medium">���</a>
<a href="new_role/human.php#guard_group">��ͷ�</a>����͡�<a href="new_role/human.php#poison_guard">����</a>��<a href="new_role/human.php#reporter">�֥�</a>��<a href="new_role/human.php#anti_voodoo">���</a>��<a href="new_role/human.php#dummy_guard">̴���</a>
<a href="new_role/human.php#common_group">��ͭ�Է�</a>����ͭ�ԡ�<a href="new_role/human.php#dummy_common">̴��ͭ��</a>
<a href="new_role/human.php#poison_group">���ǼԷ�</a>�����Ǽԡ�<a href="new_role/human.php#strong_poison">���Ǽ�</a>��<a href="new_role/human.php#incubate_poison">���Ǽ�</a>��<a href="new_role/human.php#dummy_poison">̴�Ǽ�</a>
<a href="new_role/human.php#poison_cat_group">ǭ����</a>��<a href="new_role/human.php#poison_cat">ǭ��</a>
<a href="new_role/human.php#pharmacist_group">���շ�</a>��<a href="new_role/human.php#pharmacist">����</a>
<a href="new_role/human.php#assassin_group">�Ż��Է�</a>��<a href="new_role/human.php#assassin">�Ż���</a>
<a href="new_role/human.php#mind_scanner_group">���Ȥ��</a>��<a href="new_role/human.php#mind_scanner">���Ȥ�</a>
<a href="new_role/human.php#jealousy_group">��ɱ��</a>��<a href="new_role/human.php#jealousy">��ɱ</a>
<a href="new_role/human.php#mania_group">���åޥ˥���</a>��<a href="new_role/human.php#mania">���åޥ˥�</a>��<a href="new_role/human.php#unknown_mania">�</a>
</pre>

<h4><a href="new_role/wolf.php">��ϵ�ر�</a></h4>
<pre>
<a href="new_role/wolf.php#wolf_group">��ϵ��</a>����ϵ��<a href="new_role/wolf.php#boss_wolf">��ϵ</a>��<a href="new_role/wolf.php#cursed_wolf">��ϵ</a>��<a href="new_role/wolf.php#poison_wolf">��ϵ</a>��<a href="new_role/wolf.php#resist_wolf">����ϵ</a>��<a href="new_role/wolf.php#tongue_wolf">���ϵ</a>��<a href="new_role/wolf.php#cute_wolf">˨ϵ</a>��<a href="new_role/wolf.php#silver_wolf">��ϵ</a>
<a href="new_role/wolf.php#mad_group">���ͷ�</a>�����͡�<a href="new_role/wolf.php#fanatic_mad">������</a>��<a href="new_role/wolf.php#whisper_mad">�񤭶���</a>��<a href="new_role/wolf.php#jammer_mad">����</a>��<a href="new_role/wolf.php#voodoo_mad">���ѻ�</a>��<a href="new_role/wolf.php#corpse_courier_mad">�м�</a>��<a href="new_role/wolf.php#dream_eater_mad">��</a>��<a href="new_role/wolf.php#trap_mad">櫻�</a>
</pre>

<h4><a href="new_role/fox.php">�Ÿѿر�</a></h4>
<pre>
<a href="new_role/fox.php#fox_group">�Ÿѷ�</a>���Ÿѡ�<a href="new_role/fox.php#white_fox">���</a>��<a href="new_role/fox.php#poison_fox">�ɸ�</a>��<a href="new_role/fox.php#voodoo_fox">����</a>��<a href="new_role/fox.php#cursed_fox">ŷ��</a>��<a href="new_role/fox.php#silver_fox">���</a>
<a href="new_role/fox.php#child_fox_group">�Ҹѷ�</a>��<a href="new_role/fox.php#child_fox">�Ҹ�</a>
</pre>

<h4><a href="new_role/lovers.php">���Ϳر�</a></h4>
<pre>
<a href="new_role/lovers.php#cupid_group">���塼�ԥåɷ�</a>�����塼�ԥåɡ�<a href="new_role/lovers.php#self_cupid">�ᰦ��</a>��<a href="new_role/lovers.php#mind_cupid">����</a>
</pre>

<h4><a href="new_role/quiz.php">����Կر�</a></h4>
<pre>
<a href="new_role/quiz.php#quiz_group">����Է�</a>��<a href="new_role/quiz.php#quiz">�����</a>
</pre>

<h4><a href="new_role/chiroptera.php">�����ر�</a></h4>
<pre>
<a href="new_role/chiroptera.php#chiroptera_group">������</a>��<a href="new_role/chiroptera.php#chiroptera">����</a>��<a href="new_role/chiroptera.php#poison_chiroptera">������</a>��<a href="new_role/chiroptera.php#cursed_chiroptera">������</a>
</pre>

<h2><a id="chaos_hyper"><?php echo $GAME_OPT_MESS->chaos_hyper ?></a></h2>
<h3><a id="chaos_hyper_appear_role">�и���</a></h3>
<pre>
��������Ƥ��뤹�٤Ƥ��򿦤��и����ޤ���
</pre>

<h2><a id="chaos_old"><?php echo $GAME_OPT_MESS->chaos ?></a> (��Ver. 1.4.0 ��11)</h2>
<p>
<a href="#chaos_old_appear_role">�и���</a>
<a href="#chaos_old_decide_role">�������롼����</a>
</p>
<h3><a id="chaos_old_appear_role">�и���</a></h3>
<pre>
�и������ǽ���Τ����򿦤ϰʲ��Ǥ�
</pre>
<h4><a href="new_role/human.php">¼�Ϳر�</a></h4>
<pre>
<a href="new_role/human.php#human_group">¼�ͷ�</a>��¼�͡�<a href="new_role/human.php#suspect">�Կ���</a>��<a href="new_role/human.php#unconscious">̵�ռ�</a>
<a href="new_role/human.php#mage_group">�ꤤ�շ�</a>���ꤤ�ա�<a href="new_role/human.php#soul_mage">�����ꤤ��</a>
<a href="new_role/human.php#necromancer_group">��ǽ�Է�</a>����ǽ�ԡ�<a href="new_role/human.php#medium">���</a>
<a href="new_role/human.php#guard_group">��ͷ�</a>����͡�<a href="new_role/human.php#poison_guard">����</a>��<a href="new_role/human.php#reporter">�֥�</a>
<a href="new_role/human.php#common_group">��ͭ�Է�</a>����ͭ��
<a href="new_role/human.php#poison_group">���ǼԷ�</a>�����Ǽ�
<a href="new_role/human.php#pharmacist_group">���շ�</a>��<a href="new_role/human.php#pharmacist">����</a>
<a href="new_role/human.php#mania_group">���åޥ˥���</a>��<a href="new_role/human.php#mania">���åޥ˥�</a>
</pre>

<h4><a href="new_role/wolf.php">��ϵ�ر�</a></h4>
<pre>
<a href="new_role/wolf.php#wolf_group">��ϵ��</a>����ϵ��<a href="new_role/wolf.php#boss_wolf">��ϵ</a>��<a href="new_role/wolf.php#tongue_wolf">���ϵ</a>��<a href="new_role/wolf.php#poison_wolf">��ϵ</a>��<a href="new_role/wolf.php#cute_wolf">˨ϵ</a>
<a href="new_role/wolf.php#mad_group">���ͷ�</a>�����͡�<a href="new_role/wolf.php#fanatic_mad">������</a>
</pre>

<h4><a href="new_role/fox.php">�Ÿѿر�</a></h4>
<pre>
<a href="new_role/fox.php#fox_group">�Ÿѷ�</a>���Ÿ�
<a href="new_role/fox.php#child_fox_group">�Ҹѷ�</a>��<a href="new_role/fox.php#child_fox">�Ҹ�</a>
</pre>

<h4><a href="new_role/lovers.php">���Ϳر�</a></h4>
<pre>
<a href="new_role/lovers.php#cupid_group">���塼�ԥåɷ�</a>�����塼�ԥå�
</pre>

<h3><a id="chaos_old_decide_role">�������롼����</a></h3>
<pre>
�绨�Ĥ���������ȡ��̾������ܦ���(¿���֤����Ϳ���������ȥ쥢���о�)�Ǥ���
</pre>
<p>
<a href="#chaos_wolf">��ϵ</a>
<a href="#chaos_fox">�Ÿ�</a>
<a href="#chaos_cupid">���塼�ԥå�</a>
<a href="#chaos_other">����¾</a>
</p>

<h4><a id="chaos_wolf">��ϵ</a></h4>
<pre>
�����������ݤ��ޤ� (�Ϳ��������뤴�Ȥ˥֥���礭������Τ⤢�꤫�ʡ�)
8��̤����1:2 = 80:20 (80%��1�͡�20%��2��)
8��15�͡�1:2:3 = 15:70:15 (70%��2�͡�15%��1������(1�ͤ�3��))
16��20�͡�1:2:3:4:5 = 5:10:70:10:5 (70%��3�͡�10%��1������(2�ͤ�4��)��5%��2������(1�ͤ�5��))
21�͡���70%�Ǵ��ÿͿ���10%��1��������5%��2������ (5�������뤴�Ȥ˴��ÿͿ���1�ͤ�������)
���ÿͿ� = ([(�Ϳ� - 20) / 5]���ڼΤ�) + 3
��)
24�͡�70%��3�͡�10%��1������(2�ͤ�4��)��5%��2������(1�ͤ�5��)
25�͡�70%��4�͡�10%��1������(3�ͤ�5��)��5%��2������(2�ͤ�6��)
30�͡�70%��5�͡�10%��1������(4�ͤ�6��)��5%��2������(3�ͤ�7��)
50�͡�70%��9�͡�10%��1������(8�ͤ�10��)��5%��2������(7�ͤ�11��)

���ü�ϵ�νи�Ψ
��<a href="new_role/wolf.php#boss_wolf">��ϵ</a>��<a href="new_role/wolf.php#poison_wolf">��ϵ</a>��<a href="new_role/wolf.php#tongue_wolf">���ϵ</a>������˴ޤޤ�ޤ���
([���ÿͿ� / 15] ���ھ夲)�������Ƚ���Ԥ��ޤ���
(15�ͤʤ�1��16�ͤʤ�2��50�ͤʤ�3��)
Ƚ���Ԥ����Ӥˡ����ÿͿ���Ʊ������1�͡���ϵ�������ؤ��ޤ���
��)
15�͡�15%��Ƚ���1��Ԥ���
16�͡�16%��Ƚ���2��Ԥ���
30�͡�30%��Ƚ���2��Ԥ���
50�͡�50%��Ƚ���3��Ԥ���

���ü�ϵ�γ�꿶��ˡ§
��<a href="new_role/wolf.php#boss_wolf">��ϵ</a>
<a href="new_role/wolf.php#tongue_wolf">���ϵ</a>��<a href="new_role/wolf.php#poison_wolf">��ϵ</a>�򺹤��������Ϳ������и����ޤ���

��<a href="new_role/wolf.php#tongue_wolf">���ϵ</a>�νи�Ψ
16��̤���ǤϽи����ޤ���
16�͡�20�ͤ�40%�γ�Ψ�ǽи����ޤ���
20�Ͱʾ�ǻ��ÿͿ���Ʊ�����ǽи����ޤ���(20�ͤʤ�16%��50�ͤʤ�50%)
����и��Ϳ���1�ͤǤ���

��<a href="new_role/wolf.php#poison_wolf">��ϵ</a>�νи�Ψ
20��̤���ǤϽи����ޤ���
20�Ͱʾ�ǻ��ÿͿ���Ʊ�����ǽи����ޤ���(20�ͤʤ�16%��50�ͤʤ�50%)
����и��Ϳ���1�ͤǤ���
</pre>

<h4><a id="chaos_fox">�Ÿ�</a></h4>
<pre>
��15��̤���Ϥ��ޤ˽Ф����١�����ʹߤϽи�����
15��̤����0:1 = 90:10 (90%��0�͡�10%��1��)
16��22�͡�1:2 = 90:10 (90%��1�͡�10%��2��)
23�͡���80%�Ǵ��ÿͿ���10%��1������ (20�������뤴�Ȥ˴��ÿͿ���1�ͤ�������)
���ÿͿ� = ([�Ϳ� / 20]���ھ夲)
��)
23�͡�80%��2�͡�10%��1������(1�ͤ�3��)
40�͡�80%��2�͡�10%��1������(1�ͤ�3��)
41�͡�80%��3�͡�10%��1������(2�ͤ�4��)
50�͡�80%��3�͡�10%��1������(2�ͤ�4��)

��<a href="new_role/fox.php#child_fox">�Ҹ�</a>�νи�Ψ
20��̤���ǤϽи����ޤ���
20�Ͱʾ�ǻ��ÿͿ���Ʊ�����ǽи����ޤ���(20�ͤʤ�16%��50�ͤʤ�50%)
����и��Ϳ���1�ͤǤ���
<a href="new_role/fox.php#child_fox">�Ҹ�</a>���и��������Ͻи��Ϳ���Ʊ�������ŸѤ�����ޤ���
</pre>

<h4><a id="chaos_cupid">���塼�ԥå�</a></h4>
<pre>
�������γ�Ψ�δط��ǳμ¤˽и�����Τ�40�Ͱʾ�Ȥʤ�ޤ���
(���塼�ԥåɤνи����Τ򥪥ץ���������Ǥ���褦�ˤ���ͽ��)
10��̤����0:1 = 95:5 (95%��0�͡�5%��1��)
10��16�͡�0:1 = 70:30 (70%��0�͡�30%��1��)
16��22�͡�0:1:2 = 5:90:5 (90%��1�͡�5%��1������(0�ͤ�2��))
23�͡���90%�Ǵ��ÿͿ���5%��1������ (20�������뤴�Ȥ˴��ÿͿ���1�ͤ�������)
���ÿͿ� = ([�Ϳ� / 20]���ڼΤ�)
��)
23�͡�90%��1�͡�5%��1������(0�ͤ�2��)
40�͡�90%��2�͡�5%��1������(1�ͤ�3��)
50�͡�90%��3�͡�5%��1������(1�ͤ�3��)
</pre>

<h4><a id="chaos_other">����¾</a></h4>
<pre>
���ÿͿ������ϵ���Ÿѡ����塼�ԥåɤ򺹤��������Ϳ��Ǥ���

���ꤤ��
���ꤤ�դȺ����ꤤ�դ������˴ޤޤ�ޤ���
8��̤����0:1 = 10:90 (90%��1�͡�10%��0��)
8��15�͡�1:2 = 95:5 (95%��1�͡�5%��2��)
16��29�͡�1:2 = 90:10 (90%��1�͡�10%��2��)
30�͡���80%�Ǵ��ÿͿ���10%��1������ (15�������뤴�Ȥ˴��ÿͿ���1�ͤ�������)
���ÿͿ� = ([�Ϳ� / 15]���ڼΤ�)
��)
30�͡�80%��2�͡�10%��1������(1�ͤ�3��)
50�͡�80%��3�͡�10%��1������(2�ͤ�4��)

�������ꤤ�դνи�Ψ
16��̤���ǤϽи����ޤ���
16�Ͱʾ�ǻ��ÿͿ���Ʊ�����ǽи����ޤ���(16�ͤʤ�16%��50�ͤʤ�50%)
����и��Ϳ���1�ͤǤ���
�����ꤤ�դ��и��������Ͻи��Ϳ���Ʊ�������ꤤ�դ�����ޤ���

����ǽ��
�����ߤ���ǽ�ԤΤߤ������˴ޤޤ�ޤ���
9��̤����0:1 = 10:90 (90%��1�͡�10%��0��)
9��15�͡�1:2 = 95:5 (95%��1�͡�5%��2��)
16��29�͡�1:2 = 90:10 (90%��1�͡�10%��2��)
30�͡���80%�Ǵ��ÿͿ���10%��1������ (15�������뤴�Ȥ˴��ÿͿ���1�ͤ�������)
���ÿͿ� = ([�Ϳ� / 15]���ڼΤ�)
��)
30�͡�80%��2�͡�10%��1������(1�ͤ�3��)
50�͡�80%��3�͡�10%��1������(2�ͤ�4��)

�����
�����塼�ԥåɤ��и����Ƥ�����Ϥۤܳμ¤˽и����ޤ���
��(�������0�ͤ������äƤ⶯��Ū��1�ͤ���������ޤ�)
��(��������������и����Ƥ⥭�塼�ԥåɤ��и����Ƥ���Ȥϸ¤�ޤ���)
9��̤����0:1 = 30:70 (70%��1�͡�30%��0��)
9��15�͡�0:1:2 = 10:80:10 (80%��1�͡�10%��1������(0�ͤ�2��)
16�͡���80%�Ǵ��ÿͿ���10%��1������ (15�������뤴�Ȥ˴��ÿͿ���1�ͤ�������)
���ÿͿ� = ([�Ϳ� / 15]���ڼΤ�)
��)
29�͡�80%��1�͡�10%��1������(0�ͤ�2��)
30�͡�80%��2�͡�10%��1������(1�ͤ�3��)
50�͡�80%��3�͡�10%��1������(2�ͤ�4��)

�����ͷ�
�����ͤȶ����Ԥ������˴ޤޤ�ޤ���
10��̤����0:1 = 70:30 (70%��0�͡�30%��1��)
10��15�͡�0:1:2 = 10:80:10 (80%��1�͡�10%��1������(0�ͤ�2��)
16�͡���80%�Ǵ��ÿͿ���10%��1������ (15�������뤴�Ȥ˴��ÿͿ���1�ͤ�������)
���ÿͿ� = ([�Ϳ� / 15]���ڼΤ�)
��)
29�͡�80%��1�͡�10%��1������(0�ͤ�2��)
30�͡�80%��2�͡�10%��1������(1�ͤ�3��)
50�͡�80%��3�͡�10%��1������(2�ͤ�4��)

�������Ԥνи�Ψ
16��̤���ǤϽи����ޤ���
16�Ͱʾ�ǻ��ÿͿ���Ʊ�����ǽи����ޤ���(16�ͤʤ�16%��50�ͤʤ�50%)
����и��Ϳ���1�ͤǤ���
�����Ԥ��и��������Ͻи��Ϳ���Ʊ���������ͤ�����ޤ���

����ͷ�
����ͤȵ��Τ������˴ޤޤ�ޤ���
11��̤����0:1 = 90:10 (90%��0�͡�10%��1��)
11��15�͡�0:1:2 = 10:80:10 (80%��1�͡�10%��1������(0�ͤ�2��)
16�͡���80%�Ǵ��ÿͿ���10%��1������ (15�������뤴�Ȥ˴��ÿͿ���1�ͤ�������)
���ÿͿ� = ([�Ϳ� / 15]���ڼΤ�)
��)
29�͡�80%��1�͡�10%��1������(0�ͤ�2��)
30�͡�80%��2�͡�10%��1������(1�ͤ�3��)
50�͡�80%��3�͡�10%��1������(2�ͤ�4��)

�����Τνи�Ψ
20��̤���ǤϽи����ޤ���
20�Ͱʾ�ǻ��ÿͿ���Ʊ�����ǽи����ޤ���(20�ͤʤ�20%��50�ͤʤ�50%)
����и��Ϳ���1�ͤǤ���
���Τ��и��������Ͻи��Ϳ���Ʊ��������ͤ����ǼԤ�����ޤ���

����ͭ��
13��̤����0:1 = 90:10 (90%��0�͡�10%��1��)
13��22�͡�1:2:3 = 10:80:10 (80%��2�͡�10%��1������(1�ͤ�3��)
23�͡���80%�Ǵ��ÿͿ���10%��1������ (15�������뤴�Ȥ˴��ÿͿ���1�ͤ�������)
���ÿͿ� = ([�Ϳ� / 15]���ڼΤ�) + 1
��)
29�͡�80%��2�͡�10%��1������(1�ͤ�3��)
30�͡�80%��3�͡�10%��1������(2�ͤ�4��)
50�͡�80%��4�͡�10%��1������(3�ͤ�5��)

�����Ǽ�
�����Τ��и����Ƥ������Ϥ��οͿ�ʬ�������ǼԤ�����ޤ���
16��̤����0:1 = 95:5 (95%��0�͡�5%��1��)
16��19�͡�0:1 = 85:15 (85%��0�͡�15%��1��)
20�͡���80%�Ǵ��ÿͿ���10%��1������ (20�������뤴�Ȥ˴��ÿͿ���1�ͤ�������)
���ÿͿ� = ([�Ϳ� / 20]���ڼΤ�)
��)
39�͡�80%��1�͡�10%��1������(0�ͤ�2��)
40�͡�80%��2�͡�10%��1������(1�ͤ�3��)
50�͡�80%��2�͡�10%��1������(1�ͤ�3��)

������
����ϵ���и����Ƥ�����Ϥۤܳμ¤˽и����ޤ���
��(�������0�ͤ������äƤ⶯��Ū��1�ͤ���������ޤ�)
��(�����������դ��и����Ƥ���ϵ���и����Ƥ���Ȥϸ¤�ޤ���)
16��̤����0:1 = 95:5 (95%��0�͡�5%��1��)
16��19�͡�0:1 = 85:15 (85%��0�͡�15%��1��)
20�͡���80%�Ǵ��ÿͿ���10%��1������ (20�������뤴�Ȥ˴��ÿͿ���1�ͤ�������)
���ÿͿ� = ([�Ϳ� / 20]���ڼΤ�)
��)
39�͡�80%��1�͡�10%��1������(0�ͤ�2��)
40�͡�80%��2�͡�10%��1������(1�ͤ�3��)
50�͡�80%��2�͡�10%��1������(1�ͤ�3��)

�����åޥ˥�
16��̤�����и����ޤ���
16��22�͡�0:1 = 40:60 (60%��1�͡�40%��0��)
23�͡���80%�Ǵ��ÿͿ���10%��1������ (20�������뤴�Ȥ˴��ÿͿ���1�ͤ�������)
���ÿͿ� = ([�Ϳ� / 20]���ڼΤ�)
��)
39�͡�80%��1�͡�10%��1������(0�ͤ�2��)
40�͡�80%��2�͡�10%��1������(1�ͤ�3��)
50�͡�80%��2�͡�10%��1������(1�ͤ�3��)

���Կ��Է�
���Կ��Ԥ�̵�ռ��������˴ޤޤ�ޤ���
16��̤����0:1 = 90:10 (90%��0�͡�10%��1��)
16��19�͡�0:1 = 80:20 (80%��0�͡�20%��1��)
20�͡���80%�Ǵ��ÿͿ���10%��1������ (20�������뤴�Ȥ˴��ÿͿ���1�ͤ�������)
���ÿͿ� = ([�Ϳ� / 20]���ڼΤ�)
��)
39�͡�80%��1�͡�10%��1������(0�ͤ�2��)
40�͡�80%��2�͡�10%��1������(1�ͤ�3��)
50�͡�80%��2�͡�10%��1������(1�ͤ�3��)

���Կ��ԡ�̵�ռ��νи�Ψ
20��̤���Ǥ�̵�ռ��νи�Ψ����� (̵�ռ����Կ��� = 80%:20%)��
20�Ͱʾ���Կ��Ԥνи�Ψ������� (̵�ռ����Կ��� = 40%:60%)��
�и��Ϳ��ξ�¤ϵ��ꤷ�Ƥ��ޤ���
</pre>

<h2><a id="chaos_open_cast"><?php echo $GAME_OPT_MESS->chaos_open_cast ?></a></h2>
<ol>
  <li>���������ɽ�������ر��������Τ����¤򤫤��뤳�Ȥ��Ǥ��ޤ�</li>
  <li>������̵���ס�<?php echo $GAME_OPT_CAPT->chaos_open_cast_camp ?>�ס�<?php echo $GAME_OPT_CAPT->chaos_open_cast_role ?>�ס�<?php echo $GAME_OPT_CAPT->chaos_open_cast_full ?>�פ������٤ޤ�</li>
</ol>

<h2><a id="sub_role_limit"><?php echo $GAME_OPT_MESS->sub_role_limit ?></a></h2>
<ol>
  <li>�и����륵���򿦤μ�������¤򤫤��뤳�Ȥ��Ǥ��ޤ�</li>
  <li>��<?php echo $GAME_OPT_CAPT->no_sub_role ?>�ס�<?php echo $GAME_OPT_CAPT->sub_role_limit_easy ?>�ס�<?php echo $GAME_OPT_CAPT->sub_role_limit_normal ?>�ס֥��������¤ʤ��פ������٤ޤ�</li>
  <li>���Ƥ�����ե�������ѹ��Ǥ��ޤ�</li>
</ol>
<p>
<a href="#sub_role_limit_easy"><?php echo $GAME_OPT_MESS->sub_role_limit_easy ?></a>
<a href="#sub_role_limit_normal"><?php echo $GAME_OPT_MESS->sub_role_limit_normal ?></a>
</p>

<h3><a id="sub_role_limit_easy"><?php echo $GAME_OPT_MESS->sub_role_limit_easy ?></a></h3>
<pre>
<a href="new_role/sub_role.php#decide_group">����Է�</a>��<a href="new_role/sub_role.php#authority_group">���ϼԷ�</a>�Τ߽и����ޤ���
</pre>

<h3><a id="sub_role_limit_normal"><?php echo $GAME_OPT_MESS->sub_role_limit_normal ?></a></h3>
<pre>
<a href="new_role/sub_role.php#decide_group">����Է�</a>��<a href="new_role/sub_role.php#authority_group">���ϼԷ�</a>��<a href="new_role/sub_role.php#upper_luck_group">���𺲷�</a>��<a href="new_role/sub_role.php#strong_voice_group">������</a>�Τ߽и����ޤ���
</pre>
</body></html>
