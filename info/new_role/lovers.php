<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader('���򿦾��� - [���Ϳر�]', 'new_role');
?>
</head>
<body>
<h1>���Ϳر�</h1>
<p>
<a href="./" target="_top">&lt;-��˥塼</a>
<a href="summary.php">������ɽ</a>
</p>
<p>
<a href="#cupid_group">���塼�ԥåɷ�</a>
<a href="#angel_group">ŷ�ȷ�</a>
</p>

<h2><a name="cupid_group">���塼�ԥåɷ�</a></h2>
<p>
<a href="#self_cupid">�ᰦ��</a>
<a href="#mind_cupid">����</a>
<a href="#triangle_cupid">������</a>
</p>

<h3><a name="self_cupid">�ᰦ��</a> (�ꤤ��̡�¼�� / ��ǽ��̡�¼��) [Ver. 1.4.0 ��21��]</h3>
<pre>
��ʬ�������Υ��塼�ԥåɡ�
����ä����˼�ʬ���оݤˤ���<a href="sub_role.php#mind_receiver">������</a>���դ���
</pre>
<h4>Ver. 1.4.0 ��22��</h4>
<pre>
����ä����˼�ʬ���оݤˤ���<a href="sub_role.php#mind_receiver">������</a>���Ĥ��ޤ���
</pre>
<h4>[�����Ԥ���Υ�����]</h4>
<pre>
¾�ι�˼ºߤ����򿦤Ǥ���
�оݤ����¤��������ˡ����˥�å������� (����Ū��) ���뤳�Ȥ��Ǥ��ޤ���
�פ�¸ʬ��ʬ���ۤ����äƤ���������
</pre>

<h3><a name="mind_cupid">����</a> (�ꤤ��̡�¼�� / ��ǽ��̡�¼��) [Ver. 1.4.0 ��23��]</h3>
<pre>
����ä���ͤ�<a href="sub_role.php#mind_friend">���ļ�</a>�ˤ����̥��塼�ԥåɡ�
¾�ͷ���ξ��ϡ�����˼�ʬ����ͤ��оݤˤ���<a href="sub_role.php#mind_receiver">������</a>�ˤʤ�ޤ���
</pre>
<h4>[�����Ԥ���Υ�����]</h4>
<pre>
����ǽ�Ϥ���ä����ͤ����̥��塼�ԥåɤǤ���
��ʬ����ξ���<a href="#self_cupid">�ᰦ��</a>����߷����Ʊ�ͤξ��֤ˤʤ�ޤ���
�ޤ���¾�ͷ���Ǥ�<a href="sub_role.php#mind_receiver">������</a>�ˤʤ�Τǡ�����ä��оݤ�ȯ����
ɬ�������뤳�Ȥˤʤ�ޤ���
</pre>

<h3><a name="triangle_cupid">������</a> (�ꤤ��̡�¼�� / ��ǽ��̡�¼��) [Ver. 1.4.0 ��8��]</h3>
<pre>
����ܷ�Ƥ륭�塼�ԥåɡ�
¾�ͷ�����¤ʤɤ����̤Υ��塼�ԥåɤ�Ʊ����
</pre>
<h4>[�����Ԥ���Υ�����]</h4>
<pre>
�����л�������׿�ϵ�ǰ��Ū�˻ܤ���Ƥ�����¤����̾��Ϳ���Ƥߤޤ�����
</pre>

<h2><a name="angel_group">ŷ�ȷ�</a></h2>
<p>
<a href="#angel">ŷ��</a>
<a href="#rose_angel">��ŷ��</a>
<a href="#lily_angel">ɴ��ŷ��</a>
<a href="#ark_angel">��ŷ��</a>
</p>

<h3><a name="angel">ŷ��</a> (�ꤤ��̡�¼�� / ��ǽ��̡�¼��) [Ver. 1.4.0 ��8��]</h3>
<pre>
����ä���ͤ��˽����ä�����<a href="sub_role.php#mind_sympathy">������</a>���ղä��륭�塼�ԥåɡ�
</pre>
<h4>[�����Ԥ���Υ�����]</h4>
<pre>
�Ȥ߹�碌�������ŵ���ɲä���륭�塼�ԥåɤξ�̼�Ǥ���
Ŵ�ĥ��åץ뤬�����뤳�Ȥ����äƺ������Ƥߤޤ�����
</pre>

<h3><a name="rose_angel">��ŷ��</a> (�ꤤ��̡�¼�� / ��ǽ��̡�¼��) [Ver. 1.4.0 ��8��]</h3>
<pre>
����ä���ͤ�����Ʊ�Τ��ä�����<a href="sub_role.php#mind_sympathy">������</a>���ղä��륭�塼�ԥåɡ�
</pre>
<h4>[�����Ԥ���Υ�����]</h4>
<pre>
<a href="#rose_angle">ŷ��</a>�������ǤǤ���
<a href="#rose_angle">ŷ��</a>�Ȥϵդ�ͽ�۳��Υ��åץ�󥰤������뤫�⤷��ޤ���͡�
</pre>

<h3><a name="lily_angel">ɴ��ŷ��</a> (�ꤤ��̡�¼�� / ��ǽ��̡�¼��) [Ver. 1.4.0 ��8��]</h3>
<pre>
����ä���ͤ��������ä�����<a href="sub_role.php#mind_sympathy">������</a>���ղä��륭�塼�ԥåɡ�
</pre>
<h4>[�����Ԥ���Υ�����]</h4>
<pre>
<a href="#rose_angle">ŷ��</a>�ν����ǤǤ���
<a href="human.php#sex_mage">�Ҥ褳�����</a>�к������̤򤴤ޤ��������������뤳�Ȥ˵���Ĥ��ޤ��礦��
</pre>

<h3><a name="ark_angel">��ŷ��</a> (�ꤤ��̡�¼�� / ��ǽ��̡�¼��) [Ver. 1.4.0 ��8��]</h3>
<pre>
¾��<a href="#angel_group">ŷ�ȷ�</a>����������<a href="sub_role.php#mind_sympathy">������</a>�η�̤򸫤뤳�Ȥ��Ǥ�����ŷ�ȡ�
���������ܿͤ�<a href="sub_role.php#mind_sympathy">������</a>���뤳�ȤϤǤ��ʤ���
</pre>
<h4>[�����Ԥ���Υ�����]</h4>
<pre>
<a href="#rose_angle">ŷ��</a>�κ�����ξ��֤���̾��Ϳ���Ƥߤޤ�����
�Ȥ߹�碌����ǡ������ܤ�ī��������ܾۤ������뤳�Ȥ���ǽ�ˤʤ�ޤ���
</pre>
</body></html>
