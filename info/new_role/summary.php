<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader('���򿦾��� - [����]', 'new_role');
?>
</head>
<body>

<h1>���򿦾���</h1>
<ul>
  <li>�С�����󥢥åפ��뤿�Ӥ˻��ͤ��Ѥ���ǽ��������ޤ�</li>
</ul>
<p>
<a href="../" target="_top">&lt;-TOP</a>
<a href="./" target="_top">����˥塼</a>
<a href="#table">�ḫɽ</a>
<a href="#reference">���ͥ��</a>
<a href="#memo">����ͽ����</a>
</p>
<p>
<a href="human.php">¼�Ϳر�</a>
<a href="wolf.php">��ϵ�ر�</a>
<a href="fox.php">�Ÿѿر�</a>
<a href="lovers.php">���Ϳر�</a>
<a href="quiz.php">����Կر�</a>
<a href="chiroptera.php">�����ر�</a>
<a href="sub_role.php">������</a>
</p>

<h2><a name="table">�ḫɽ</a></h2>
<p>
<a href="#main_role">�ᥤ����</a>
Ver. 1.4.0
<a href="#140alpha2">��2</a>
<a href="#140alpha3">��3-7</a>
<a href="#140alpha9">��9</a>
<a href="#140alpha11">��11</a>
<a href="#140alpha12">��12</a>
<a href="#140alpha13">��13</a>
<a href="#140alpha14">��14</a>
<a href="#140alpha17">��17</a>
<a href="#140alpha18">��18</a>
<a href="#140alpha19">��19</a>
<a href="#140alpha20">��20</a>
<a href="#140alpha21">��21</a>
<a href="#140alpha22">��22</a>
<a href="#140alpha23">��23</a>
<a href="#140alpha24">��24</a>
<a href="#140beta2">��2</a>
<a href="#140beta5">��5</a>
<a href="#140beta6">��6</a>
<a href="#140beta7">��7</a>
<a href="#140beta8">��8</a>
</p>

<p>
<a href="#sub_role">������</a>
Ver. 1.4.0
<a href="#sub_140alpha3">��3-7</a>
<a href="#sub_140alpha9">��9</a>
<a href="#sub_140alpha11">��11</a>
<a href="#sub_140alpha14">��14</a>
<a href="#sub_140alpha15">��15</a>
<a href="#sub_140alpha17">��17</a>
<a href="#sub_140alpha19">��19</a>
<a href="#sub_140alpha21">��21</a>
<a href="#sub_140alpha22">��22</a>
<a href="#sub_140alpha23">��23</a>
<a href="#sub_140beta2">��2</a>
<a href="#sub_140beta8">��8</a>
</p>

<table>
<caption><a name="main_role">�����ḫɽ</a></caption>
  <tr>
    <th>̾��</th>
    <th>�ر�</th>
    <th>��°</th>
    <th>�ꤤ���</th>
    <th>��ǽ���</th>
    <th>ǽ��</th>
    <th>���о�</th>
  </tr>
  <tr>
    <td><a href="quiz.php#quiz" name="140alpha2">�����</a></td>
    <td><a href="quiz.php">�����</a></td>
    <td><a href="quiz.php#quiz_group">����Է�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">������¼�� GM��</td>
    <td>Ver. 1.4.0 ��2</td>
  </tr>
  <tr>
    <td><a href="wolf.php#boss_wolf" name="140alpha3">��ϵ</a></td>
    <td><a href="wolf.php">��ϵ</a></td>
    <td><a href="wolf.php#wolf_group">��ϵ��</a></td>
    <td>¼��</td>
    <td>��ϵ</td>
    <td class="ability">�ꤤ��̤���¼�͡ס���ǽ��̤�����ϵ�פȽФ��ϵ��</td>
    <td>Ver. 1.4.0 ��3-7</td>
  </tr>
  <tr>
    <td><a href="human.php#soul_mage">�����ꤤ��</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#mage_group">�ꤤ�շ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ä��ͤ��򿦤�ʬ�������ꤤ�ա�<br>
      �ŸѤ�����Ǥ��ʤ������֤��ϼ����롣</td>
    <td>Ver. 1.4.0 ��3-7</td>
  </tr>
  <tr>
    <td><a href="human.php#medium">���</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#necromancer_group">��ǽ�Է�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�����ष���ͤν�°�رĤ�ʬ�����ü����ǽ�ԡ�</td>
    <td>Ver. 1.4.0 ��3-7</td>
  </tr>
  <tr>
    <td><a href="human.php#poison_guard">����</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#guard_group">��ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�Ǥ���ä���̼�� (�ߤ��Ƥ��Ǥ�ȯư���ʤ�)��<br>
      <a href="#guard_limit">�������</a>�αƶ�������ʤ���</td>
    <td>Ver. 1.4.0 ��3-7</td>
  </tr>
  <tr>
    <td><a href="wolf.php#fanatic_mad">������</a></td>
    <td><a href="wolf.php">��ϵ</a></td>
    <td><a href="wolf.php#mad_group">���ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ϵ��ï��ʬ�����̶��� (��ϵ����϶����Ԥ�ʬ����ʤ�)��</td>
    <td>Ver. 1.4.0 ��3-7</td>
  </tr>
  <tr>
    <td><a href="fox.php#child_fox">�Ҹ�</a></td>
    <td><a href="fox.php">�Ÿ�</a></td>
    <td><a href="fox.php#child_fox_group">�Ҹѷ�</a></td>
    <td>¼��<br>(����̵��)</td>
    <td>�Ҹ�</td>
    <td class="ability">��������ʤ�����ϵ�˽��⤵���Ȼ�������Ÿѡ�<br>
      ��֤�ʬ���뤬ǰ�äϤǤ��ʤ����ꤤ�����뤬�������Ԥ��롣</td>
    <td>Ver. 1.4.0 ��3-7</td>
  </tr>
  <tr>
    <td><a href="human.php#suspect" name="140alpha9">�Կ���</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#human_group">¼�ͷ�</a></td>
    <td>��ϵ</td>
    <td>¼��</td>
    <td class="ability">�ꤤ�դ˿�ϵ��Ƚ�ꤵ��Ƥ��ޤ�¼�� (ɽ���ϡ�¼�͡�)��<br>
      ���Ψ��ȯ�������ʤ��������ؤ�äƤ��ޤ� (<a href="wolf.php#cute_wolf">˨ϵ</a>��Ʊ��)��</td>
    <td>Ver. 1.4.0 ��9</td>
  </tr>
  <tr>
    <td><a href="human.php#mania" name="140alpha11">���åޥ˥�</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#mania_group">���åޥ˥���</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">���������ï����ͤ�����Ǥ��οͤ��򿦤򥳥ԡ����� (�����ؤ��Τ� 2 ���ܤ�ī)��</td>
    <td>Ver. 1.4.0 ��11</td>
  </tr>
  <tr>
    <td><a href="wolf.php#poison_wolf" name="140alpha12">��ϵ</a></td>
    <td><a href="wolf.php">��ϵ</a></td>
    <td><a href="wolf.php#wolf_group">��ϵ��</a></td>
    <td>��ϵ</td>
    <td>��ϵ</td>
    <td class="ability">�Ǥ���ä���ϵ���Ǥ��оݤ�ϵ�ʳ���</td>
    <td>Ver. 1.4.0 ��12</td>
  </tr>
  <tr>
    <td><a href="human.php#pharmacist">����</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#pharmacist_group">���շ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�����ɼ�����ͤ��Ǥ���äƤ��뤫��ī��ʬ���롣<br>
      �ǻ������ߤä��Ȥ��ˡ���ɼ���Ƥ�������� (�Ǥ�ȯư���ʤ�) ���롣
    </td>
    <td>Ver. 1.4.0 ��12</td>
  </tr>
  <tr>
    <td><a href="human.php#unconscious" name="140alpha13">̵�ռ�</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#human_group">¼�ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ϵ��̵�ռ��Ǥ��뤳�Ȥ�ʬ���äƤ��ޤ�¼�� (ɽ���ϡ�¼�͡�)��</td>
    <td>Ver. 1.4.0 ��13</td>
  </tr>
  <tr>
    <td><a href="wolf.php#tongue_wolf">���ϵ</a></td>
    <td><a href="wolf.php">��ϵ</a></td>
    <td><a href="wolf.php#wolf_group">��ϵ��</a></td>
    <td>��ϵ</td>
    <td>��ϵ</td>
    <td class="ability">���������ɼ��Ԥä����Τߡ��������˳�����ͤ��򿦤�ʬ���롣<br>
      ����˼��Ԥ�����̵����¼�ͤ򽱷⤹���ǽ�Ϥ򼺤���</td>
    <td>Ver. 1.4.0 ��13</td>
  </tr>
  <tr>
    <td><a href="human.php#reporter" name="140alpha14">�֥�</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#guard_group">��ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">���Ԥ����ͤ����ޤ줿���ˡ��������ϵ��ï��ʬ�����ü�ʼ�͡�<br>
      �����Ĥ��ʤ����ͳ� (ϵ�ȸ�) �����Ԥ����黦����롣</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="human.php#dummy_mage">̴����</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#mage_group">�ꤤ�շ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��¼�͡פȡֿ�ϵ�פ�ȿž������̤��Ф��ꤤ�� (ɽ���ϡ��ꤤ�ա�)��<br>
      �����Ǥ��ʤ�����˼������ꤤ˸���αƶ�������ʤ���</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="wolf.php#cute_wolf">˨ϵ</a></td>
    <td><a href="wolf.php">��ϵ</a></td>
    <td><a href="wolf.php#wolf_group">��ϵ��</a></td>
    <td>��ϵ</td>
    <td>��ϵ</td>
    <td class="ability">���Ψ��ȯ�������ʤ��������ؤ�äƤ��ޤ���</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="human.php#dummy_necromancer" name="140alpha17">̴���</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#necromancer_group">��ǽ�Է�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��¼�͡פȡֿ�ϵ�פ�ȿž������̤��Ф���ǽ�� (ɽ���ϡ���ǽ�ԡ�)��<br>
      <a href="wolf.php#corpse_courier_mad">�м�</a>��˸���αƶ�������ʤ���</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="human.php#dummy_guard">̴���</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#guard_group">��ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ͤȻפ�����Ǥ���¼�� (ɽ���ϡּ�͡�)��<br>
      ��˸��������å��������Ф뤬ï���Ҥ��Ƥ��ʤ���
      ���餫�η���<a href="wolf.php#dream_eater_mad">��</a>���ܿ��������ϼ�뤳�Ȥ��Ǥ��롣</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="human.php#dummy_common">̴��ͭ��</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#common_group">��ͭ�Է�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�������������귯�ζ�ͭ�ԡפȻפ�����Ǥ���¼�͡�<br>
      ��ͭ�Ԥ��񤭤������ʤ���</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="human.php#dummy_poison">̴�Ǽ�</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#poison_group">���ǼԷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">���ǼԤȻפ�����Ǥ���¼�� (ɽ���ϡ����Ǽԡ�)��</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="human.php#soul_necromancer">������</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#necromancer_group">��ǽ�Է�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�跺�����ͤ��򿦤�ʬ��������ǽ�ԡ�</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="human.php#strong_poison">���Ǽ�</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#poison_group">���ǼԷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�ߤ�줿���˿ͳ� (ϵ�ȸ�) �Τߤ򴬤����������Ǽԡ�<br>
      ɽ���ϡ����Ǽԡפǡ�¼�ͤ������ä�����ȯ��</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="human.php#incubate_poison">���Ǽ�</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#poison_group">���ǼԷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�����ФĤ� (���ߤ� 5 ���ܰʹ�) <a href="human.php#strong_poison">���Ǽ�</a>�������Ǥ����¼�͡�</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="wolf.php#resist_wolf">����ϵ</a></td>
    <td><a href="wolf.php">��ϵ</a></td>
    <td><a href="wolf.php#wolf_group">��ϵ��</a></td>
    <td>��ϵ</td>
    <td>��ϵ</td>
    <td class="ability">���٤����Ǥ��Ѥ������ϵ��</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="wolf.php#cursed_wolf">��ϵ</a></td>
    <td><a href="wolf.php">��ϵ</a></td>
    <td><a href="wolf.php#wolf_group">��ϵ��</a></td>
    <td>��ϵ<br>(���֤�)</td>
    <td>��ϵ</td>
    <td class="ability">���줿����ä��ꤤ�դ����������ϵ��<br>
      <a href="human.php#voodoo_killer">���ۻ�</a>�����줿�黦����롣</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="wolf.php#whisper_mad">�񤭶���</a></td>
    <td><a href="wolf.php">��ϵ</a></td>
    <td><a href="wolf.php#mad_group">���ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ϵ��������̤˻��äǤ����̶��͡�</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="fox.php#cursed_fox">ŷ��</a></td>
    <td><a href="fox.php">�Ÿ�</a></td>
    <td><a href="fox.php#fox_group">�Ÿѷ�</a></td>
    <td>¼��<br>(���֤�)</td>
    <td>�Ÿ�</td>
    <td class="ability">���줿����ä��ꤤ�դ���������Ÿѡ�<br>
      ��ϵ�˽��⤵��Ƥ��ʤʤ�����<a href="human.php#voodoo_killer">���ۻ�</a>������뤫��ͤ˸�Ҥ����Ȼ�����롣</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="fox.php#poison_fox">�ɸ�</a></td>
    <td><a href="fox.php">�Ÿ�</a></td>
    <td><a href="fox.php#fox_group">�Ÿѷ�</a></td>
    <td>¼��<br>(����)</td>
    <td>¼��</td>
    <td class="ability">�Ǥ���ä��Ÿѡ�<br>
      �ߤ�줿�����Ǥ��оݤϸѰʳ���</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="fox.php#white_fox">���</a></td>
    <td><a href="fox.php">�Ÿ�</a></td>
    <td><a href="fox.php#fox_group">�Ÿѷ�</a></td>
    <td>¼��<br>(����̵��)</td>
    <td>�Ÿ�</td>
    <td class="ability">��������ʤ�����ϵ�˽��⤵���Ȼ�������Ÿѡ�</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="human.php#poison_cat" name="140alpha18">ǭ��</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#poison_cat_group">ǭ����</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">����ǽ�Ϥ���ä��ü�����Ǽԡ�<br>
    ��������Ψ�� 25% ���٤�������ͤȰ㤦�ͤ����褹�뤳�Ȥ⤢�롣</td>
    <td>Ver. 1.4.0 ��18</td>
  </tr>
  <tr>
    <td><a href="human.php#assassin">�Ż���</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#assassin_group">�Ż��Է�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">���¼�ͤ�Ż��Ǥ���¼�͡�<br>
    �ͳ� (ϵ���) �Ǥ�Ż��Ǥ��뤬��ͤθ�Ҥ�������ʤ���</td>
    <td>Ver. 1.4.0 ��18</td>
  </tr>
  <tr>
    <td><a href="human.php#psycho_mage">���������</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#mage_group">�ꤤ�շ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">¼�ͤο������֤�Ƚ�ꤹ���ü���ꤤ�ա�<br>
      ���͡�̴�ϡ�<a href="human.php#suspect">�Կ���</a>��<a href="human.php#unconscious">̵�ռ�</a>���ꤦ�ȡֱ���Ĥ��Ƥ���פ�Ƚ�ꤵ��롣</td>
    <td>Ver. 1.4.0 ��18</td>
  </tr>
  <tr>
    <td><a href="wolf.php#trap_mad">櫻�</a></td>
    <td><a href="wolf.php">��ϵ</a></td>
    <td><a href="wolf.php#mad_group">���ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">���٤������¼�Ͱ�ͤ�櫤�ųݤ��뤳�Ȥ��Ǥ����ü�ʶ��͡�<br>
      櫤�ųݤ����ͤθ���ˬ�줿��ϵ����ͷ�(��͡�<a href="human.php#poison_guard">����</a>��<a href="human.php#reporter">�֥�</a>) <a href="human.php#assassin">�Ż���</a>�ϻ�˴���롣��ͤ˸�Ҥ����Ȼ�����롣</td>
    <td>Ver. 1.4.0 ��18</td>
  </tr>
  <tr>
    <td><a href="wolf.php#jammer_mad" name="140alpha19">����</a></td>
    <td><a href="wolf.php">��ϵ</a></td>
    <td><a href="wolf.php#mad_group">���ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">���¼�Ͱ�ͤ����ӡ����οͤ��ꤤ��ư��˸�������ü�ʶ��͡�<br>
      ��ͤ˸�Ҥ����Ȼ�����롣��21������ⶸ�� �� ���� ���ѹ���</td>
    <td>Ver. 1.4.0 ��19</td>
  </tr>
  <tr>
    <td><a href="human.php#sex_mage">�Ҥ褳�����</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#mage_group">�ꤤ�շ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">¼�ͤ����̤�Ƚ�̤����ü���ꤤ�ա�<br>
      <a href="chiroptera.php">����</a>���ꤦ�ȡ������פ�Ƚ�ꤵ��롣</td>
    <td>Ver. 1.4.0 ��19</td>
  </tr>
  <tr>
    <td><a href="wolf.php#voodoo_mad" name="140alpha20">���ѻ�</a></td>
    <td><a href="wolf.php">��ϵ</a></td>
    <td><a href="wolf.php#mad_group">���ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">���¼�Ͱ�ͤ����ӡ����οͤ˼����򤫤����ü�ʶ��͡�<br>
      ����줿�ͤ���ä��ꤤ�դϼ��֤�������롣��ͤ˸�Ҥ����Ȼ�����롣</td>
    <td>Ver. 1.4.0 ��20</td>
  </tr>
  <tr>
    <td><a href="fox.php#voodoo_fox">����</a></td>
    <td><a href="fox.php">�Ÿ�</a></td>
    <td><a href="fox.php#fox_group">�Ÿѷ�</a></td>
    <td>¼��<br>(����)</td>
    <td>¼��</td>
    <td class="ability">���¼�Ͱ�ͤ����ӡ����οͤ˼����򤫤����Ÿѡ�<br>
      ����줿�ͤ���ä��ꤤ�դϼ��֤�������롣
      ��ϵ�˽��⤵��Ƥ��ʤʤ�������ͤ˸�Ҥ����Ȼ�����롣</td>
    <td>Ver. 1.4.0 ��20</td>
  </tr>
  <tr>
    <td><a href="human.php#anti_voodoo">���</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#guard_group">��ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��Ҥ����ͤ��� (�ꤤ˸�������֤������) ��㱤��ü�ʼ�͡�<br>
      �����������ϼ����������ѤΥ����ƥ��å�������ɽ������롣
    </td>
    <td>Ver. 1.4.0 ��20</td>
  </tr>
  <tr>
    <td><a href="human.php#voodoo_killer">���ۻ�</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#mage_group">�ꤤ�շ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�м���������ü���ꤤ�ա�<br>
      ��ä��ͤ�����������<a href="wolf.php#possessed_wolf">��ϵ</a>�ξ��ϼ�������ï���˼����򤫤����Ƥ������ϲ�����ơ����֤���ȯư�򥭥�󥻥뤹�롣����������������������Τߡ������������ѤΥ����ƥ��å�������ɽ������롣�����λ�˴��å������ϡּ��֤��פ�Ʊ����
    </td>
    <td>Ver. 1.4.0 ��20</td>
  </tr>
  <tr>
    <td><a href="human.php#yama_necromancer">����</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#necromancer_group">��ǽ�Է�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�����λ�Ԥλ����ʬ�����ü����ǽ�ԡ�<br>
      ����ϲ��̤β���ɽ�������֡���̵�Ĥʡ��פβ��ιԤˡ�(���Ͽ�ϵ�˽��⤵�줿�褦�Ǥ�)������ɽ������롣</td>
    <td>Ver. 1.4.0 ��20</td>
  </tr>
  <tr>
    <td><a href="fox.php#silver_fox">���</a></td>
    <td><a href="fox.php">�Ÿ�</a></td>
    <td><a href="fox.php#fox_group">�Ÿѷ�</a></td>
    <td>¼��<br>(����)</td>
    <td>¼��</td>
    <td class="ability">��֤�ʬ����ʤ��Ÿѡ�<br>
      (¾���Ÿѡ�<a href="fox.php#child_fox">�Ҹ�</a>�������֤Ǥ����ʬ����ʤ�)</td>
    <td>Ver. 1.4.0 ��20</td>
  </tr>
  <tr>
    <td><a href="lovers.php#self_cupid" name="140alpha21">�ᰦ��</a></td>
    <td><a href="lovers.php">����</a></td>
    <td><a href="lovers.php#cupid_group">���塼�ԥåɷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ʬ�������Υ��塼�ԥåɡ�����Ǥä�����<a href="sub_role.php#mind_receiver">������</a>���ɲä���롣</td>
    <td>Ver. 1.4.0 ��21</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#chiroptera">����</a></td>
    <td><a href="chiroptera.php">����</a></td>
    <td><a href="chiroptera.php#chiroptera_group">������</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�����Ĥä��龡���ˤʤ롣<br>¾�οرĤξ��ԤȤ϶��礷�ʤ���</td>
    <td>Ver. 1.4.0 ��21</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#poison_chiroptera">������</a></td>
    <td><a href="chiroptera.php">����</a></td>
    <td><a href="chiroptera.php#chiroptera_group">������</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�Ǥ���ä��������ߤ�줿�����Ǥ��оݤϿͳ� (ϵ�ȸ�) ��������<br>
      ��ͤ˸�Ҥ����Ȼ�����롣</td>
    <td>Ver. 1.4.0 ��21</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#cursed_chiroptera">������</a></td>
    <td><a href="chiroptera.php">����</a></td>
    <td><a href="chiroptera.php#chiroptera_group">������</a></td>
    <td>¼��<br>(���֤�)</td>
    <td>¼��</td>
    <td class="ability">���줿����ä��ꤤ�դ��������������<br>
      <a href="human.php#voodoo_killer">���ۻ�</a>������뤫����ͤ˸�Ҥ����Ȼ�����롣</td>
    <td>Ver. 1.4.0 ��21</td>
  </tr>
  <tr>
    <td><a href="wolf.php#silver_wolf">��ϵ</a></td>
    <td><a href="wolf.php">��ϵ</a></td>
    <td><a href="wolf.php#wolf_group">��ϵ��</a></td>
    <td>��ϵ</td>
    <td>��ϵ</td>
    <td class="ability">��֤�ʬ����ʤ���ϵ��<br>
      (¾�ο�ϵ��<a href="wolf.php#fanatic_mad">������</a>��<a href="wolf.php#whisper_mad">�񤭶���</a>�������֤Ǥ����ʬ����ʤ�)</td>
    <td>Ver. 1.4.0 ��21</td>
  </tr>
  <tr>
    <td><a href="human.php#mind_scanner">���Ȥ�</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#mind_scanner_group">���Ȥ��</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">���������ï����ͤ�����Ǥ��οͤ�<a href="sub_role.php#mind_read">���ȥ��</a>�ˤ��롣<br>
      ��ɼ��̤��Ф�Τ� 2 ���ܰʹߤǡ�<a href="human.php#unconscious">̵�ռ�</a>��ȯ�����ɤ�ʤ���ϵ�α��ʤ��������ʤ���
    </td>
    <td>Ver. 1.4.0 ��21</td>
  </tr>
  <tr>
    <td><a href="wolf.php#corpse_courier_mad">�м�</a></td>
    <td><a href="wolf.php">��ϵ</a></td>
    <td><a href="wolf.php#mad_group">���ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ʬ����ɼ�����ߤä��ͤ���ǽ��̤��äǤ����ü�ʶ��͡�<br>
      <a href="human.php#dummy_necromancer">̴���</a>�ˤϱƶ����ʤ�����ͤ˸�Ҥ����Ȼ�����롣</td>
    <td>Ver. 1.4.0 ��21</td>
  </tr>
  <tr>
    <td><a href="wolf.php#dream_eater_mad">��</a></td>
    <td><a href="wolf.php">��ϵ</a></td>
    <td><a href="wolf.php#mad_group">���ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�����ɼ����̴��ǽ�ϼԤ򻦤����Ȥ��Ǥ����ü�ʶ��͡�<br>
      ���餫�η���<a href="human.php#dummy_guard">̴���</a>���ܿ��������ϻ�����롣��ͤ˸�Ҥ����Ȼ�����롣</td>
    <td>Ver. 1.4.0 ��21</td>
  </tr>
  <tr>
    <td><a href="human.php#jealousy" name="140alpha22">��ɱ</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#jealousy_group">��ɱ��</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�����ɼ���ˡ�Ʊ�쥭�塼�ԥåɤ����ͤ�·�äƼ�ʬ����ɼ��������ɼ�������ͤ򥷥�å��व���롣�ߤ�줿����̵����</td>
    <td>Ver. 1.4.0 ��22</td>
  </tr>
  <tr>
    <td><a href="human.php#unknown_mania" name="140alpha23">�</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#mania_group">���åޥ˥���</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">���������ï����ͤ�����Ǥ��οͤ�Ʊ����°�رĤˤ��롣<br>
     ��ʬ����ɼ���<a href="sub_role.php#mind_friend">���ļ�</a>���Ĥ� (��̤�ɽ�������Τ� 2 ���ܤ�ī)��</td>
    <td>Ver. 1.4.0 ��23</td>
  </tr>
  <tr>
    <td><a href="lovers.php#mind_cupid">����</a></td>
    <td><a href="lovers.php">����</td>
    <td><a href="lovers.php#cupid_group">���塼�ԥåɷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">����ä���ͤ�<a href="sub_role.php#mind_friend">���ļ�</a>�ˤ����̥��塼�ԥåɡ�<br>
      ¾�ͷ���ξ��ϡ�����˼�ʬ����ͤ�<a href="sub_role.php#mind_receiver">������</a>�ˤʤ롣</td>
    <td>Ver. 1.4.0 ��23</td>
  </tr>
  <tr>
    <td><a href="human.php#priest" name="140alpha24">�ʺ�</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#priest_group">�ʺ׷�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�����������Ȥ˸��ߡ���¸���Ƥ���¼�ͿرĤοͿ���ʬ���롣<br>
     ���ߤ� 4���ܰʹߡ�1������ (4 �� 6 �� 8 ��...)��</td>
    <td>Ver. 1.4.0 ��24</td>
  </tr>
  <tr>
    <td><a href="fox.php#scarlet_fox">�ȸ�</a></td>
    <td><a href="fox.php">�Ÿ�</td>
    <td><a href="fox.php#fox_group">�Ÿѷ�</a></td>
    <td>¼��<br>(����)</td>
    <td>¼��</td>
    <td class="ability">��ϵ�����<a href="human.php#unconscious">̵�ռ�</a>�˸������Ÿѡ�</td>
    <td>Ver. 1.4.0 ��24</td>
  </tr>
  <tr>
    <td><a href="wolf.php#wise_wolf">��ϵ</a></td>
    <td><a href="wolf.php">��ϵ</td>
    <td><a href="wolf.php#wolf_group">��ϵ��</a></td>
    <td>��ϵ</td>
    <td>��ϵ</td>
    <td class="ability"><a href="fox.php">�Ÿѿر�</a>��ǰ�ä���ͭ�Ԥ��񤭤��Ѵ������ʹ�������ϵ��</td>
    <td>Ver. 1.4.0 ��24</td>
  </tr>
  <tr>
    <td><a href="wolf.php#possessed_wolf">��ϵ</a></td>
    <td><a href="wolf.php">��ϵ</td>
    <td><a href="wolf.php#wolf_group">��ϵ��</a></td>
    <td>��ϵ</td>
    <td>��ϵ</td>
    <td class="ability">���⤬���������齱�⤷���ͤ��ü���ϵ��<br>
      �����귯��<a href="human.php#revive_priest">ŷ��</a>���ŸѤϾ�ü��ʤ���</td>
    <td>Ver. 1.4.0 ��24</td>
  </tr>
  <tr>
    <td><a href="fox.php#cute_fox">˨��</a></td>
    <td><a href="fox.php">�Ÿ�</td>
    <td><a href="fox.php#fox_group">�Ÿѷ�</a></td>
    <td>¼��<br>(����)</td>
    <td>¼��</td>
    <td class="ability">���Ψ��ȯ�������ʤ��������ؤ�äƤ��ޤ��Ÿѡ�<br>
    ���ʤ������Ƥ�<a href="human.php#suspect">�Կ���</a>��<a href="wolf.php#cute_wolf">˨ϵ</a>��Ʊ����</td>
    <td>Ver. 1.4.0 ��24</td>
  </tr>
  <tr>
    <td><a href="fox.php#black_fox">����</a></td>
    <td><a href="fox.php">�Ÿ�</td>
    <td><a href="fox.php#fox_group">�Ÿѷ�</a></td>
    <td>��ϵ<br>(����̵��)</td>
    <td>�Ÿ�</td>
    <td class="ability">�ꤤ��̤��ֿ�ϵ�ס���ǽ��̤����Ÿѡפ�Ƚ�ꤵ����Ÿѡ�</td>
    <td>Ver. 1.4.0 ��24</td>
  </tr>
  <tr>
    <td><a href="wolf.php#scarlet_wolf">��ϵ</a></td>
    <td><a href="wolf.php">��ϵ</td>
    <td><a href="wolf.php#wolf_group">��ϵ��</a></td>
    <td>��ϵ</td>
    <td>��ϵ</td>
    <td class="ability"><a href="fox.php">�Ÿѿر�</a>����<a href="fox.php#child_fox">�Ҹ�</a>�˸������ϵ��</td>
    <td>Ver. 1.4.0 ��24</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#dummy_chiroptera">̴�ᰦ��</a></td>
    <td><a href="chiroptera.php">����</a></td>
    <td><a href="chiroptera.php#chiroptera_group">������</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�ܿͤˤ�<a href="lovers.php#self_cupid">�ᰦ��</a>��ɽ������Ƥ���������<br>
      ����Ĥ��ȤϤǤ��뤬���ͤˤϤʤ餺��<a href="sub_role.php#mind_receiver">������</a>��Ĥ��ʤ���</td>
    <td>Ver. 1.4.0 ��24</td>
  </tr>
  <tr>
    <td><a href="human.php#crisis_priest" name="140beta2">�¸���</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#priest_group">�ʺ׷�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�����ֿͳ����������פ�ʬ�����ü�ʻʺ� (ɽ���ϡ�¼�͡�)��<br>
      �ֿͳ����������Ǥ���פ�Ƚ�ꤵ�줿���ϡ��ɤοرĤ�ͭ���ʤΤ���å�������ɽ������롣</td>
    <td>Ver. 1.4.0 ��2</td>
  </tr>
  <tr>
    <td><a href="human.php#revive_priest">ŷ��</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#priest_group">�ʺ׷�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�����ܤ�ī�ˤ����ʤ��˴���ơ��ֿͳ����������ס�5���ܰʹߡסֿ͸�Ⱦ���ס�LW�פΤɤ줫���������������֤��ü�ʻʺס�<br>
      ���ͤˤʤ��ǽ�Ϥ򼺤���</td>
    <td>Ver. 1.4.0 ��2</td>
  </tr>
  <tr>
    <td><a href="human.php#evoke_scanner">������</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#mind_scanner_group">���Ȥ��</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">���������ï����ͤ�����Ǥ��οͤ�<a href="sub_role.php#mind_evoke">����</a>�ˤ��롣<br>
      ��ɼ��̤��Ф�Τ� 2 ���ܰʹߡ���ʬ�ΰ����˲���ɽ������Ƥ��Ƥ����ϻĤ�ʤ���</td>
    <td>Ver. 1.4.0 ��2</td>
  </tr>
  <tr>
    <td><a href="human.php#revive_cat">��ì</a></td>
    <td><a href="human.php">¼��</a></td>
    <td><a href="human.php#poison_cat_group">ǭ����</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ǽ�Ϥ򼺤ä�����˹⤤����ǽ�Ϥ���ä�ǭ���ξ�̼<br>
    ��������Ψ�� 80% ���������������뤿�Ӥ�����Ψ�� 1/4 �ˤʤ롣</td>
    <td>Ver. 1.4.0 ��2</td>
  </tr>
  <tr>
    <td><a href="fox.php#revive_fox">���</a></td>
    <td><a href="fox.php">�Ÿ�</a></td>
    <td><a href="fox.php#fox_group">�Ÿѷ�</a></td>
    <td>¼��<br>(����)</td>
    <td>¼��</td>
    <td class="ability">����ǽ�Ϥ���ä��Ÿѡ�����Ψ�� 100% �������������������ǽ�Ϥ򼺤���<br>
      ��ϵ�˽��⤵��Ƥ��ʤʤ�������ͤ˸�Ҥ����Ȼ�����롣</td>
    <td>Ver. 1.4.0 ��2</td>
  </tr>
  <tr>
    <td><a href="human.php#elder" name="140beta5">ĹϷ</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#human_group">¼�ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ɼ���� +1 �����¼�͡�</td>
    <td>Ver. 1.4.0 ��5</td>
  </tr>
  <tr>
    <td><a href="wolf.php#elder_wolf" name="140beta5">��ϵ</a></td>
    <td><a href="wolf.php">��ϵ</td>
    <td><a href="wolf.php#wolf_group">��ϵ��</a></td>
    <td>��ϵ</td>
    <td>��ϵ</td>
    <td class="ability">��ɼ���� +1 ������ϵ��</td>
    <td>Ver. 1.4.0 ��5</td>
  </tr>
  <tr>
    <td><a href="fox.php#elder_fox">�Ÿ�</a></td>
    <td><a href="fox.php">�Ÿ�</a></td>
    <td><a href="fox.php#fox_group">�Ÿѷ�</a></td>
    <td>¼��<br>(����)</td>
    <td>¼��</td>
    <td class="ability">��ɼ���� +1 ������Ÿѡ�</td>
    <td>Ver. 1.4.0 ��5</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#elder_chiroptera">������</a></td>
    <td><a href="chiroptera.php">����</a></td>
    <td><a href="chiroptera.php#chiroptera_group">������</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ɼ���� +1 �����������</td>
    <td>Ver. 1.4.0 ��5</td>
  </tr>
  <tr>
    <td><a href="human.php#fend_guard">Ǧ��</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#guard_group">��ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">���٤�����ϵ�ν�����Ѥ�������Ǥ����͡�</td>
    <td>Ver. 1.4.0 ��5</td>
  </tr>
  <tr>
    <td><a href="human.php#trap_common" name="140beta6">����</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#common_group">��ͭ�Է�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">¼�Ϳرİʳ��ο����Ƥ�����ɼ���줿��ޤȤ�ƻ�˴�������̶�ͭ�ԡ�</td>
    <td>Ver. 1.4.0 ��6</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#fairy">����</a></td>
    <td><a href="chiroptera.php">����</a></td>
    <td><a href="chiroptera.php#fairy_group">������</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ɼ���ȯ������Ƭ�˶�ͭ�Ԥ��񤭤��ɲä��롣<br>
      �������ꤤ˸������ʧ���αƶ�������롣</td>
    <td>Ver. 1.4.0 ��6</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#spring_fairy">������</a></td>
    <td><a href="chiroptera.php">����</a></td>
    <td><a href="chiroptera.php#fairy_group">������</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ɼ���ȯ������Ƭ�ˡֽդǤ��衼�פ��ɲä���������</td>
    <td>Ver. 1.4.0 ��6</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#summer_fairy">������</a></td>
    <td><a href="chiroptera.php">����</a></td>
    <td><a href="chiroptera.php#fairy_group">������</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ɼ���ȯ������Ƭ�ˡֲƤǤ��衼�פ��ɲä���������</td>
    <td>Ver. 1.4.0 ��6</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#autumn_fairy">������</a></td>
    <td><a href="chiroptera.php">����</a></td>
    <td><a href="chiroptera.php#fairy_group">������</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ɼ���ȯ������Ƭ�ˡֽ��Ǥ��衼�פ��ɲä���������</td>
    <td>Ver. 1.4.0 ��6</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#winter_fairy">������</a></td>
    <td><a href="chiroptera.php">����</a></td>
    <td><a href="chiroptera.php#fairy_group">������</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ɼ���ȯ������Ƭ�ˡ��ߤǤ��衼�פ��ɲä���������</td>
    <td>Ver. 1.4.0 ��6</td>
  </tr>
  <tr>
    <td><a href="human.php#ghost_common">˴���</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#common_group">��ͭ�Է�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability"><a href="wolf.php#wolf_group">��ϵ</a>�˽��⤵�줿�齱�⤷�Ƥ�����ϵ��<a href="sub_role.php#chicken">������</a>���ղä����̶�ͭ�ԡ�</td>
    <td>Ver. 1.4.0 ��6</td>
  </tr>
  <tr>
    <td><a href="human.php#poison_jealousy">�Ƕ�ɱ</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#jealousy_group">��ɱ��</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">���ͤΤߤ�������Ǽԡ��ܿͤ�ɽ���ϡ����Ǽԡס�</td>
    <td>Ver. 1.4.0 ��6</td>
  </tr>
  <tr>
    <td><a href="human.php#chain_poison">Ϣ�Ǽ�</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#poison_group">���ǼԷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">¾����ǽ�ϼԤ˴������ޤ줿�顢�������ʹ����������Ǽԡ��ܿͤ�ɽ���ϡ�¼�͡ס�</td>
    <td>Ver. 1.4.0 ��6</td>
  </tr>
  <tr>
    <td><a href="human.php#saint" name="140beta7">����</a></td>
    <td><a href="human.php">¼��</td>
    <td><a href="human.php#human_group">¼�ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">����ɼ�κ�¿��ɼ�Ԥˤʤä����ˡ������ˤ�ä��ߤ���ͤ��Ѳ�������¼�͡��ܿͤ�ɽ���ϡ�¼�͡ס�</td>
    <td>Ver. 1.4.0 ��7</td>
  </tr>
  <tr>
    <td><a href="wolf.php#agitate_mad">��ư��</a></td>
    <td><a href="wolf.php">��ϵ</td>
    <td><a href="wolf.php#mad_group">���ͷ�</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">����ɼ�κ�¿��ɼ�Ԥ���ɼ���Ƥ������ˡ���ɼ����ߤꡢ����ʳ��κ�¿��ɼ�Ԥ�ޤȤ�ƥ���å��व�����ü�ʶ��͡�<br>
      ��ͤ˸�Ҥ����Ȼ�����롣</td>
    <td>Ver. 1.4.0 ��7</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#light_fairy">������</a></td>
    <td><a href="chiroptera.php">����</a></td>
    <td><a href="chiroptera.php#fairy_group">������</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ɼ�褬��ϵ�˽��⤵�줿�顢�����������������(����<a href="sub_role.php#mind_open">������</a>) �ˤ���������</td>
    <td>Ver. 1.4.0 ��7</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#dark_fairy">������</a></td>
    <td><a href="chiroptera.php">����</a></td>
    <td><a href="chiroptera.php#fairy_group">������</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">��ɼ�褬��ϵ�˽��⤵�줿�顢�����������־��ǡ�(����<a href="sub_role.php#blinder">�ܱ���</a>) �ˤ���������</td>
    <td>Ver. 1.4.0 ��7</td>
  </tr>
  <tr>
    <td><a href="chiroptera.php#mirror_fairy">������</a></td>
    <td><a href="chiroptera.php">����</a></td>
    <td><a href="chiroptera.php#fairy_group">������</a></td>
    <td>¼��</td>
    <td>¼��</td>
    <td class="ability">�ܿͤ��ߤ�줿�顢�����������ַ�����ɼ��(��������ɼ������ͤˤ�����ɼ�Ǥ��ʤ�) �ˤ���������</td>
    <td>Ver. 1.4.0 ��7</td>
  </tr>
  <tr>
    <td><a href="wolf.php#emerald_wolf" name="140beta8">��ϵ</a></td>
    <td><a href="wolf.php">��ϵ</td>
    <td><a href="wolf.php#wolf_group">��ϵ��</a></td>
    <td>��ϵ</td>
    <td>��ϵ</td>
    <td class="ability">������ͤ�ϵ���ä����˼�ʬ�ȳ�����ͤ�<a href="sub_role.php#mind_friend">���ļ�</a>�ˤ����ϵ��</td>
    <td>Ver. 1.4.0 ��8</td>
  </tr>
  <tr>
    <td><a href="wolf.php#blue_wolf">��ϵ</a></td>
    <td><a href="wolf.php">��ϵ</td>
    <td><a href="wolf.php#wolf_group">��ϵ��</a></td>
    <td>��ϵ</td>
    <td>��ϵ</td>
    <td class="ability">������ͤ�<a href="fox.php#silver_fox">���</a>�ʳ��γ��߻����ʤ��ŸѤ��ä�����<a href="sub_role.php#mind_lonely">�Ϥ����</a>���ղä����ϵ��</td>
    <td>Ver. 1.4.0 ��8</td>
  </tr>
  <tr>
    <td><a href="fox.php#emerald_fox">���</a></td>
    <td><a href="fox.php">�Ÿ�</a></td>
    <td><a href="fox.php#fox_group">�Ÿѷ�</a></td>
    <td>¼��<br>(����)</td>
    <td>¼��</td>
    <td class="ability">��ä��ͤ����äǤ��ʤ��ŸѤ��ä����˼�ʬ����ä��ͤ�<a href="sub_role.php#mind_friend">���ļ�</a>�ˤ����Ÿѡ�<br>
    ����ȯư�����ǽ�Ϥ򼺤���
    </td>
    <td>Ver. 1.4.0 ��8</td>
  </tr>
  <tr>
    <td><a href="fox.php#blue_fox">���</a></td>
    <td><a href="fox.php">�Ÿ�</a></td>
    <td><a href="fox.php#fox_group">�Ÿѷ�</a></td>
    <td>¼��<br>(����)</td>
    <td>¼��</td>
    <td class="ability"><a href="wolf.php#wolf_group">��ϵ</a>�˽��⤵�줿�齱�⤷�Ƥ�����ϵ��<a href="sub_role.php#mind_lonely">�Ϥ����</a>�ˤ����Ÿѡ�</td>
    <td>Ver. 1.4.0 ��8</td>
  </tr>
</table>

<table>
<caption><a name="sub_role">���������ḫɽ</a></caption>
  <tr>
    <th>̾��</th>
    <th>��°</th>
    <th>ɽ��</th>
    <th>ǽ��</th>
    <th>���о�</th>
  </tr>
  <tr>
    <td><a href="sub_role.php#strong_voice" name="sub_140alpha3">����</a></td>
    <td><a href="sub_role.php#strong_voice_group">������</a></td>
    <td>��</td>
    <td class="ability">��������ˤʤ�</td>
    <td>Ver. 1.4.0 ��3-7</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#normal_voice">�Դ���</a></td>
    <td><a href="sub_role.php#strong_voice_group">������</a></td>
    <td>��</td>
    <td class="ability">ȯ�����礭�����Ѥ����ʤ�</td>
    <td>Ver. 1.4.0 ��3-7</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#weak_voice">����</a></td>
    <td><a href="sub_role.php#strong_voice_group">������</a></td>
    <td>��</td>
    <td class="ability">��˾����ˤʤ�</td>
    <td>Ver. 1.4.0 ��3-7</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#chicken">������</a></td>
    <td><a href="sub_role.php#chicken_group">�����Է�</a></td>
    <td>��</td>
    <td class="ability">�����ɼ���˰�ɼ�Ǥ��㤦�ȥ���å��ह��</td>
    <td>Ver. 1.4.0 ��3-7</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#rabbit">������</a></td>
    <td><a href="sub_role.php#chicken_group">�����Է�</a></td>
    <td>��</td>
    <td class="ability">�����ɼ���˰�ɼ���㤨�ʤ��ȥ���å��ह��</td>
    <td>Ver. 1.4.0 ��3-7</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#perverseness">ŷ�ٵ�</a></td>
    <td><a href="sub_role.php#chicken_group">�����Է�</a></td>
    <td>��</td>
    <td class="ability">�����ɼ����¾�οͤ���ɼ�褬���ȥ���å��ह��</td>
    <td>Ver. 1.4.0 ��3-7</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#no_last_words" name="sub_140alpha9">ɮ����</a></td>
    <td><a href="sub_role.php#no_last_words_group">ɮ������</a></td>
    <td>��</td>
    <td class="ability">�����Ĥ��ʤ�</td>
    <td>Ver. 1.4.0 ��9</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#watcher">˵�Ѽ�</a></td>
    <td><a href="sub_role.php#authority_group">���ϼԷ�</a></td>
    <td>��</td>
    <td class="ability">��ɼ���� 0 �ˤʤ�</td>
    <td>Ver. 1.4.0 ��9</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#plague">���¿�</a></td>
    <td><a href="sub_role.php#decide_group">����Է�</a></td>
    <td>��</td>
    <td class="ability">�跺�Ը��䤬ʣ���������˼�ʬ����ɼ�褬�ߤ���䤫����������</td>
    <td>Ver. 1.4.0 ��9</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#liar" name="sub_140alpha11">ϵ��ǯ</a></td>
    <td><a href="sub_role.php#liar_group">ϵ��ǯ��</a></td>
    <td>��</td>
    <td class="ability">ȯ�����ˡֿ͢�ϵ�����������ؤ�� (���ޤ��Ѵ�����ʤ����Ȥ⤢��)</td>
    <td>Ver. 1.4.0 ��11</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#random_voice" name="sub_140alpha14">���¼�</a></td>
    <td><a href="sub_role.php#strong_voice_group">������</a></td>
    <td>��</td>
    <td class="ability">�����礭������������Ѥ��</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#earplug">����</a></td>
    <td><a href="sub_role.php#no_last_words_group">ɮ������</a></td>
    <td>��</td>
    <td class="ability">ȯ�������ʳ�������������褦�ˤʤꡢ������ʹ�����ʤ��ʤ�</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#good_luck">����</a></td>
    <td><a href="sub_role.php#decide_group">����Է�</a></td>
    <td>��</td>
    <td class="ability">��ʬ����¿��ɼ�Ԥǽ跺�Ը��䤬ʣ�����������ߤ���䤫����������</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#bad_luck">�Ա�</a></td>
    <td><a href="sub_role.php#decide_group">����Է�</a></td>
    <td>��</td>
    <td class="ability">��ʬ����¿��ɼ�Ԥǽ跺�Ը��䤬ʣ����������ͥ��Ū���ߤ���</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#upper_luck">����</a></td>
    <td><a href="sub_role.php#upper_luck_group">���𺲷�</a></td>
    <td>��</td>
    <td class="ability">2���ܤ���ɼ���� +4 ���������ˡ�3���ܰʹߤ� -2 �����</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#downer_luck">��ȯ��</a></td>
    <td><a href="sub_role.php#upper_luck_group">���𺲷�</a></td>
    <td>��</td>
    <td class="ability">2���ܤ���ɼ���� -4 ���������ˡ�3���ܰʹߤ� +2 �����</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#star">�͵���</a></td>
    <td><a href="sub_role.php#upper_luck_group">���𺲷�</a></td>
    <td>��</td>
    <td class="ability">��ɼ���� -1 �����</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#disfavor">�Կ͵�</a></td>
    <td><a href="sub_role.php#upper_luck_group">���𺲷�</a></td>
    <td>��</td>
    <td class="ability">��ɼ���� +1 �����</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#random_voter">��ʬ��</a></td>
    <td><a href="sub_role.php#authority_group">���ϼԷ�</a></td>
    <td>��</td>
    <td class="ability">��ɼ���� -1��+1 ���ϰϤǥ������������������</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#rebel">ȿ�ռ�</a></td>
    <td><a href="sub_role.php#authority">���ϼԷ�</a></td>
    <td>��</td>
    <td class="ability">���ϼԤ�Ʊ���ͤ���ɼ�������˼�ʬ�ȸ��ϼԤ���ɼ���� 0 �ˤʤ�</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#gentleman">�»�</a></td>
    <td><a href="sub_role.php#liar_group">ϵ��ǯ��</a></td>
    <td>��</td>
    <td class="ability">����ȯ�����ֿ»Ρפʸ��դ������ؤ��</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#lady">�ʽ�</a></td>
    <td><a href="sub_role.php#liar_group">ϵ��ǯ��</a></td>
    <td>��</td>
    <td class="ability">����ȯ�����ֽʽ��פʸ��դ������ؤ��</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#blinder">�ܱ���</a></td>
    <td><a href="sub_role.php#no_last_words_group">ɮ������</a></td>
    <td>��</td>
    <td class="ability">ȯ���Ԥ�̾���������ʤ� (����˸�����)</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#silent">̵��</a></td>
    <td><a href="sub_role.php#no_last_words_group">ɮ������</a></td>
    <td>��</td>
    <td class="ability">ȯ����ʸ���������¤�������</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#invisible">�����º�</a></td>
    <td><a href="sub_role.php#liar_group">ϵ��ǯ��</a></td>
    <td>��</td>
    <td class="ability">ȯ���ΰ���������������ؤ��</td>
    <td>Ver. 1.4.0 ��14</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#random_luck" name="sub_140alpha15">��������</a></td>
    <td><a href="sub_role.php#upper_luck_group">���𺲷�</a></td>
    <td>��</td>
    <td class="ability">��ɼ���� -2��+2 ���ϰϤǥ������������������</td>
    <td>Ver. 1.4.0 ��15</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#flattery">���ޤ���</a></td>
    <td><a href="sub_role.php#chicken_group">�����Է�</a></td>
    <td>��</td>
    <td class="ability">�����ɼ������ɼ�褬ï�Ȥ���äƤ��ʤ��ȥ���å��ह��</td>
    <td>Ver. 1.4.0 ��15</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#impatience">û��</a></td>
    <td><a href="sub_role.php#chicken_group">�����Է�</a></td>
    <td>��</td>
    <td class="ability">����Ԥ�Ʊ����ǽ�Ϥ���������˺���ɼ�ˤʤ�ȥ���å��ह��</td>
    <td>Ver. 1.4.0 ��15</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#speaker" name="sub_140alpha17">���ԡ�����</a></td>
    <td><a href="sub_role.php#no_last_words_group">ɮ������</a></td>
    <td>��</td>
    <td class="ability">ȯ�������ʳ��礭��������褦�ˤʤꡢ������ʹ�����ʤ��ʤ�</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#upper_voice">�ᥬ�ۥ�</a></td>
    <td><a href="sub_role.php#strong_voice_group">������</a></td>
    <td>��</td>
    <td class="ability">ȯ�������ʳ��礭���ʤꡢ�����ϲ���줷��ʹ�����ʤ��ʤ�</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#downer_voice">�ޥ���</a></td>
    <td><a href="sub_role.php#strong_voice_group">������</a></td>
    <td>��</td>
    <td class="ability">ȯ�������ʳ��������ʤꡢ������ʹ�����ʤ��ʤ�</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#rainbow">�����º�</a></td>
    <td><a href="sub_role.php#liar_group">ϵ��ǯ��</a></td>
    <td>��</td>
    <td class="ability">ȯ�������ο����ޤޤ�Ƥ��������ν��֤˹�碌�������ؤ����Ƥ��ޤ�</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#panelist">������</a></td>
    <td><a href="sub_role.php#chicken_group">�����Է�</a></td>
    <td>��</td>
    <td class="ability">��ɼ���� 0 �ˤʤꡢ����Ԥ���ɼ�����饷��å��ह��<br>
    ������¼���ѡ�</td>
    <td>Ver. 1.4.0 ��17</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#weekly" name="sub_140alpha19">�����º�</a></td>
    <td><a href="sub_role.php#liar_group">ϵ��ǯ��</a></td>
    <td>��</td>
    <td class="ability">ȯ�����������ޤޤ�Ƥ����������ν��֤˹�碌�������ؤ����Ƥ��ޤ�</td>
    <td>Ver. 1.4.0 ��19</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_read" name="sub_140alpha21">���ȥ��</a></td>
    <td><a href="sub_role.php#mind_read_group">���ȥ���</a></td>
    <td>��</td>
    <td class="ability"><a href="human.php#mind_scanner">���Ȥ�</a>�����ȯ���������Ƥ��ޤ�</td>
    <td>Ver. 1.4.0 ��21</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_open" name="sub_140alpha22">������</a></td>
    <td><a href="sub_role.php#mind_read_group">���ȥ���</a></td>
    <td>��</td>
    <td class="ability">�����ܰʹߤ����ȯ�������ü������˸����Ƥ��ޤ�</td>
    <td>Ver. 1.4.0 ��22</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_receiver">������</a></td>
    <td><a href="sub_role.php#mind_read_group">���ȥ���</a></td>
    <td>��</td>
    <td class="ability">����οͤ����ȯ����������褦�ˤʤ�</td>
    <td>Ver. 1.4.0 ��22</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#celibacy">�ȿȵ�²</a></td>
    <td><a href="sub_role.php#chicken_group">�����Է�</a></td>
    <td>��</td>
    <td class="ability">�����ɼ�������ͤ����ɼ�Ǥ��㤦�ȥ���å��ह��</td>
    <td>Ver. 1.4.0 ��22</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#inside_voice" name="sub_140alpha23">���۷�</a></td>
    <td><a href="sub_role.php#strong_voice_group">������</a></td>
    <td>��</td>
    <td class="ability">��Ͼ�������������ˤʤ�</td>
    <td>Ver. 1.4.0 ��23</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#outside_voice">���۷�</a></td>
    <td><a href="sub_role.php#strong_voice_group">������</a></td>
    <td>��</td>
    <td class="ability">�����������Ͼ����ˤʤ�</td>
    <td>Ver. 1.4.0 ��23</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mower">�𴢤�</a></td>
    <td><a href="sub_role.php#no_last_words_group">ɮ������</a></td>
    <td>��</td>
    <td class="ability">ȯ�������w�פ������</td>
    <td>Ver. 1.4.0 ��23</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#grassy">���º�</a></td>
    <td><a href="sub_role.php#liar_group">ϵ��ǯ��</a></td>
    <td>��</td>
    <td class="ability">ȯ���ΰ�ʸ����ˡ�w�פ��ղä����</td>
    <td>Ver. 1.4.0 ��23</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#side_reverse">�����º�</a></td>
    <td><a href="sub_role.php#liar_group">ϵ��ǯ��</a></td>
    <td>��</td>
    <td class="ability">ȯ����ʸ�����¤Ӥ����ñ�̤ǵդˤʤ�</td>
    <td>Ver. 1.4.0 ��23</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#line_reverse">ŷ���º�</a></td>
    <td><a href="sub_role.php#liar_group">ϵ��ǯ��</a></td>
    <td>��</td>
    <td class="ability">ȯ���ιԤ��¤Ӥξ岼�������ؤ��</td>
    <td>Ver. 1.4.0 ��23</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_friend">���ļ�</a></td>
    <td><a href="sub_role.php#mind_read_group">���ȥ���</a></td>
    <td>��</td>
    <td class="ability">����οͤ���˲��äǤ���褦�ˤʤ�</td>
    <td>Ver. 1.4.0 ��23</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_evoke" name="sub_140beta2">����</a></td>
    <td><a href="sub_role.php#mind_read_group">���ȥ���</a></td>
    <td>��</td>
    <td class="ability">��������οͤΰ����˥�å������������褦�ˤʤ�</td>
    <td>Ver. 1.4.0 ��2</td>
  </tr>
  <tr>
    <td><a href="sub_role.php#mind_lonely" name="sub_140beta8">�Ϥ����</a></td>
    <td><a href="sub_role.php#mind_read_group">���ȥ���</a></td>
    <td>��</td>
    <td class="ability">��֤�ʬ����ʤ��ʤꡢ���äǤ��ʤ��ʤ�</td>
    <td>Ver. 1.4.0 ��8</td>
  </tr>
</table>

<h2><a name="reference">���ͥ��</a></h2>
<pre>
��ä��ߤ����򿦤ʤɤ����ä��餳����Υ���ؤɤ���
<a href="http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1246414115/l50" target="_top">�ڥͥ����ޡۤ��ä��餤���ʡ�������򿦡ڥ����紿�ޡ�</a>

���ͥ���å�
<a href="http://jbbs.livedoor.jp/bbs/read.cgi/game/48159/1243197597/l50" target="_top">������ƥ���åɡ������</a>
</pre>

<h2><a name="memo">����ͽ����</a></h2>
<h3>��</h3>
<h4>����ͽ��</h4>
<pre>
��Ͷ�Ǽ� (¼�Ϳر� / ¼�ͷϡ�)
���Ǥ�¼����ä�������������ǻ��

���ȶ��ꤤ�� (¼�Ϳر� / �ꤤ�շ�)
���оݤ�����¼�Ǥɤ줯�餤�����Ǥ����ǽ�������뤫��ȶ���Ƚ�ꤹ��

�����Ǽ� (¼�Ϳر� / ���շϡ�)
���Ǥ����ä��������ʤ�¼��

����� (¼�Ϳر� / �Ż���)
����ɼ������ 3 ����˻��
����ɼ��˰Ż�ͽ��ɽ�������
���Ƥ���ɼ�������̿����Ӥ�

���Ż��� (¼�Ϳر� / �Ż���)
��¼�ͿرĤ򻦤�����ǽ�Ϥ򼺤� (��̤Ȥ��ƿͳ��򻦤����������ФǤ���)

�����ü� (¼�Ϳر� / ���åޥ˥���)
��5���ܤ˥��ԡ���ξ�̿����Ѳ�����
����̿���������򿦤����������ѹ�����

��̴���� (¼�Ϳر� / ���åޥ˥���)
��5���ܤ˥��ԡ����̴�����������Ѳ�����
��̴����������������򿦤����������ѹ�����

��ŷϵ (��ϵ�ر� / ��ϵ��)
��LW �ˤʤä�����̵�����������ǽ�Ϥ����

�����Ÿ�ϵ (��ϵ�ر� / ��ϵ��)
���ŸѤ������ϵ
����������μ�����Ĥ���
���ŸѿرĤ���ϵ�رĤ��Ф��ƶ��ϤˤʤäƤ������������

������ (�Ÿѿر� / �Ÿѷϡ�)
����ɱǽ�Ϥ��ä��Ÿ�

������ (�Ÿѿر� / �Ҹ�)
���Ҥ褳����Τ�ǽ�Ϥ��ä��Ҹ�

�������� (�����ر� / ������)
�����ޤ줿��¾�������˿�����ˤʤäƤ�餦
��������ܺ٤ʻ��ͤ��׸�Ƥ
</pre>

<h4>����ͽ��</h4>
<pre>
��ť�� (�쥹 13)
��ư�ϰ��罸��

����Ƹ (�쥹 17)
��ư�ϰ��罸��

���ߥ��㥰������ (�쥹 19)
���� (�쥹 33)

�������� (�쥹 33)
��ư�ϰ��罸��
���� (�쥹 43)

���붵�� (�쥹 86)
��ư��̤��

����͸� (�쥹 89)
</pre>
<h4>���ѻװ���</h4>
<pre>
������ѻ� (�쥹 13)

������ (�쥹 13)
���� (�쥹 64)

��굻� (�쥹 68)

�����Ƥʻ�� (�쥹85)
</pre>


<h3>¼������</h3>
<h4>����ͽ��</h4>
<pre>
������¼ (�쥹 65)
</pre>
</body></html>
