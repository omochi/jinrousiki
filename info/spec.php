<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('MESSAGE');
OutputInfoPageHeader('�ܺ٤ʻ���');
?>
<p>
<a href="#decide_role">�������롼����</a>
<a href="#dummy_boy">�����귯 (GM)</a>
<a href="#dead">�������</a>
<a href="#vote">��ɼ</a>
<a href="#revive_refuse">�������ॷ���ƥ�</a>
</p>

<h2><a name="decide_role">�������롼����</a></h2>
<p>
<a href="#decide_role_room">¼</a>
<a href="#decide_role_dummy_boy">�����귯</a>
<a href="#decide_role_user">�桼��</a>
</p>

<h3><a name="decide_role_room">¼</a></h3>
</p>
<ol>
<li>���ÿͿ������</li>
<li>�Ϳ�������ꤵ��Ƥ�������ǡ�������� (<a href="../rule.php" target="_top">�롼��</a>����)</li>
<li>�ü�¼�ʤ����ƺ����ؤ���</li>
<li>�̾�¼�ʤ饲���४�ץ����˱����Ƹ��̤������ؤ���</li>
<li>�������</li>
</ol>

<h3><a name="decide_role_dummy_boy">�����귯</a></h3>
<ol>
<li>��������</li>
<li>�����������ꥹ�Ȥ���</li>
<li>�����귯���ʤ���򿦤�������ޤ���Ƭ��������å�</li>
<li>���ƥ����å����Ƹ��Ĥ���ʤ���Х��顼���֤�</li>
<li>�������
</ol>

<h3><a name="decide_role_user">�桼��</a></h3>
<ol>
<li>�����귯���������ꤷ�ƥ桼���ꥹ�Ȥ���ַ���Ѥߥꥹ�ȡפذ�ư</li>
<li>������ʥ桼���ꥹ�Ȥ���</li>
<li>�ꥹ�Ȥ���Ƭ�οͤδ�˾�򿦤��ǧ</li>
<li>������˾���Ƥƶ���������Ф��οͤ��򤬷��ꡢ�ַ���Ѥߥꥹ�ȡפذ�ư</li>
<li>��˾�ʤ����������ʤ���С�̤����ꥹ�ȡפذ�ư</li>
<li>�������꽪�������̤����ꥹ�ȡפοͤ�;����꿶��</li>
</ol>

<h2><a name="dummy_boy">�����귯 (GM) �λ���</a></h2>
<ul>
<li>����������ཪλ�������ξ��󤬸����ޤ�</li>
<li>�����೫�����Υ桼���Ρ��򿦡פϡִ�˾�򿦡פǤ�</li>
<li>ñ�Ȥ� KICK ��ɼ�ǥ桼���򽳤�����ޤ�</li>
<li>��������ϡְ����ȯ���򤹤�����ѥ����ƥ��å������ˤʤ�ޤ�</li>
<li>��ɼǽ�Ϥ������򿦤Ǥ��äƤ���ɼ���뤳�ȤϤǤ��ޤ���</li>
</ul>

<h2><a name="dead">�������</a></h2>
<p>
<a href="#dead_common">����</a>
<a href="#dead_day">��</a>
<a href="#dead_night">��</a>
</p>

<h3><a name="dead_common">����</a></h3>
<h4>��<?php echo $MESSAGE->sudden_death ?></h4>
<ul>
<li>������ (��ɼ˺��)</li>
</ul>

<h4>��<?php echo $MESSAGE->lovers_followed ?></h4>
<ul>
<li>���ɤ� (����)</li>
</ul>


<h3><a name="dead_day">��</a></h3>
<h4>��<?php echo $MESSAGE->vote_killed ?></h4>
<ul>
<li>�跺 (�����ɼ)</li>
</ul>

<h4>��<?php echo $MESSAGE->deadman ?></h4>
<ul>
<li>�� (<a href="new_role/human.php#poison_group">���ǼԷ�</a>)</li>
<li>� (<a href="new_role/human.php#trap_common">����</a>)</li>
</ul>

<h4>��<?php echo $MESSAGE->vote_sudden_death ?></h4>
<ul>
<li>����å��� (<a href="new_role/sub_role.php#chicken_group">�����Է�</a>��<a href="new_role/human.php#jealousy">��ɱ</a>��<a href="new_role/wolf.php#agitate_mad">��ư��</a>)</li>
</ul>

<h3><a name="dead_night">��</a></h3>
<h4>��<?php echo $MESSAGE->deadman ?></h4>
<ul>
<li>���� (<a href="new_role/wolf.php#wolf_group">��ϵ��</a>)</li>
<li>��ϵ���� (<a href="new_role/wolf.php#hungry_wolf">��ϵ</a>)</li>
<li>�� (<a href="new_role/human.php#poison_group">���ǼԷ�</a>)</li>
<li>� (<a href="new_role/wolf.php#trap_mad">櫻�</a>)</li>
<li><a href="new_role/human.php#guard_hunt">���</a> (<a href="new_role/human.php#guard_group">��ͷ�</a>)</li>
<li>�Ż� (<a href="new_role/human.php#assassin_group">�Ż��Է�</a>)</li>
<li>̴���� (<a href="new_role/wolf.php#dream_eater_mad">��</a>)</li>
<li>���� (<a href="new_role/human.php#mage_group">�ꤤ�շ�</a>)</li>
<li>���֤� (<a href="new_role/wolf.php#cursed_wolf">��ϵ</a>�ʤɤμ���������<a href="new_role/wolf.php#voodoo_mad">���ѻ�</a>�ʤɤμ���ǽ�ϼ�)</li>
<li>��� (<a href="new_role/wolf.php#possessed_wolf">��ϵ</a>�ʤ�)</li>
<li>��Ͳ��� (<a href="new_role/human.php#anti_voodoo">���</a>)</li>
<li>���� (<a href="new_role/human.php#revive_priest">ŷ��</a>)</li>
<li>�ͳ����� (<a href="new_role/human.php#reporter">�֥�</a>)</li>
<li>������ (<a href="new_role/human.php#sacrifice_cat">ǭ��</a>��<a href="new_role/human.php#doll_master">�ͷ�����</a>��<a href="new_role/chiroptera.php#boss_chiroptera">������</a>)</li>
</ul>
<h4>��<?php echo $MESSAGE->revive_success ?></h4>
<ul>
<li>���� (<a href="new_role/human.php#poison_cat_group">ǭ����</a>��<a href="new_role/fox.php#revive_fox">���</a>��<a href="new_role/human.php#revive_priest">ŷ��</a>)</li>
</ul>

<h4>��<?php echo $MESSAGE->revive_failed ?></h4>
<ul>
<li>�������� (����餷�������ʤ�) (<a href="new_role/human.php#poison_cat_group">ǭ����</a>��<a href="new_role/fox.php#revive_fox">���</a>)</li>
</ul>

<h2><a name="vote">��ɼ�����λ���</a></h2>
<p>
<a href="#vote_legend">Ƚ��</a>
<a href="#vote_day">��</a>
<a href="#vote_night">��</a>
</p>

<h3><a name="vote_legend">Ƚ��</a></h3>
<ul>
  <li>�֢��׻�������ñ��</li>
  <li>�֡��Ƚ��ͥ���� (Ƚ����)</li>
</ul>

<h3><a name="vote_day">��</a></h3>
<pre>
+ �������
  - ��ɼ���� �� �跺�Է��� �� ��Ƚ�� �� ���ɤ�

+ �跺�Է���ˡ§
  - ñ�ȥȥå� �� ����� �� <a href="new_role/sub_role.php#bad_luck">�Ա�</a> �� <a href="new_role/sub_role.php#impatience">û��</a> �� <a href="new_role/sub_role.php#good_luck">����</a>��ƨ��� �� <a href="new_role/sub_role.php#plague">���¿�</a>����ɼ�褬ƨ���

+ ��Ƚ���
  - <a href="new_role/human.php#executor">���Լ�</a> �� <a href="new_role/human.php#saint">����</a> �� <a href="new_role/wolf.php#agitate_mad">��ư��</a> �� <a href="new_role/human.php#pharmacist">����</a> �� ����Ƚ�� �� ��ȯưȽ�� �� <a href="new_role/human.php#trap_common">����</a> �� <a href="new_role/human.php#jealousy">��ɱ</a> �� <a href="new_role/sub_role.php#chicken_group">����å���</a>

</pre>

<h3><a name="vote_night">��</a></h3>
<pre>
+ �������
  - ���� �� �ܿ� �� ̴ �� �ꤤ �� &lt;���ˤ��̽���&gt; �� ��� �� ���ɤ� �� �ʺ�
    &lt;[����] ���ԡ� �� ���� / [�����ܰʹ�] ���� �� ȿ�� �� ����&gt;

+ ���� (���塼�ԥåɷ�)
  - ��ߺ��ѤϤʤ��Τ���ɼľ��˽�����Ԥ�

+ �ܿ� (��ϵ����͡��Ż��ԡ�櫻�)
  - � �� ��͸�� �� ��ϵ���� �� ��ͤμ�� �� �Ż�

+ ̴ (̴��͡���)
  - ̴��͸�� �� �ӽ��� �� ̴��ͤμ��

+ �ꤤ (�ꤤ�ϡ���������ơ����ѷ�)
  - ��ʧ�� �� �ꤤ˸�� �� ���� �� �ꤤ (����)
</pre>

<h2><a name="revive_refuse">�������ॷ���ƥ�</a></h2>
<pre>
��˴�塢����վ��֤λ�����ɼ���̤򥯥�å������
���������ह���(�ǥե����) �Ȥ����ܥ��󤬽и����ޤ���
����򥯥�å�����ȡ֥����ƥࡧ��������������ष�ޤ����פȤ���
�ȯ������������ޤ���

���ξ��֤Ǥ��οͤ�����������Ф줿���� 100% �����˼��Ԥ��ޤ���
��ͤ˴ؤ��륷���ƥ����ȤʤäƤ��ޤ����ᡢ�����ˤϹ��Τ��ޤ���

����ϡ���˴��˵ޤ��ѻ������ä�ȴ���ʤ���Фʤ�ʤ��ͤΰ٤εߺ����֤Ǥ���
</pre>
</body></html>
