<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
OutputHTMLHeader($SERVER_CONF->title . $SERVER_CONF->comment, 'index');
?>
</head>
<body>
<a href="../">TOP �����</a>
<p>Ver. 1.4.0 ��24 ���åץ��� (2010/01/28 (Thu) 21:29:30) �� <a href="../src/">���������</a><br>
����ϵ����ϵ����ϵ���ȸѡ����ѡ��ʺפμ���������ϵ�λ����ѹ��ʤ�<br>
<br>
���Х� Fix<br>
��game_play.php % 731 ����<br>
�� $USERS->GetHandleName($target_uname) . '�������ɼ�Ѥ�');<br>
�� $USERS->GetHandleName($target_uname, true) . '�������ɼ�Ѥ�');<br>
<br>
��include/game_functions.php % 705 ����<br>
��elseif($pseud_self->IsRole('wise_wolf')){<br>
��elseif($virtual_self->IsRole('wise_wolf')){<br>
<br>
��user_manager.php % 276 ���� (2010/01/30 02:30 �ɵ�)<br>
�� array_push($wish_role_list, 'mage', 'necromancer', 'priest', 'common', 'poison',<br>
�� array_push($wish_role_list, 'mage', 'necromancer', 'priest', 'guard', 'common', 'poison',<br>
<br>
��include/game_functions.php % 400 �����ն� (2010/02/01 (Mon) 00:15 �ɵ�)<br>
[before]<br>
$said_user = $USERS->ByVirtualUname($talk->uname);<br>
[after]<br>
if(strpos($talk->location, 'heaven') === false)<br>
  $said_user = $USERS->ByVirtualUname($talk->uname);<br>
else<br>
  $said_user = $USERS->ByUname($talk->uname);<br>
<br>
��include/game_vote_functions % 1865 �����ն�<br>
[before]<br>
$target->dead_flag = false; //��˴�ե饰��ꥻ�å�<br>
$USERS->Kill($target->user_no, 'WOLF_KILLED');<br>
if($target->revive_flag) $target->Update('live', 'live'); //�����б�<br>
[after]<br>
if(isset($target->user_no)){<br>
  $target->dead_flag = false; //��˴�ե饰��ꥻ�å�<br>
  $USERS->Kill($target->user_no, 'WOLF_KILLED');<br>
  if($target->revive_flag) $target->Update('live', 'live'); //�����б�<br>
}<br>


</p>

<p>Ver. 1.4.0 ��23 ���åץ��� (2010/01/10 (Sun) 06:11:24) �� <a href="../src/">���������</a><br>
�����Ȥꡦ��ϵ�����դλ����ѹ���󬡦�����μ����ʤ�
</p>
</body></html>
