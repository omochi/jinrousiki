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
</p>

<h2><a name="cupid_group">���塼�ԥåɷ�</a></h2>
<p>
<a href="#self_cupid">�ᰦ��</a>
<a href="#mind_cupid">����</a>
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
</body></html>
