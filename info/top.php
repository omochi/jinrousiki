<p>
  <font color="#FF0000">
   �����ϥƥ������ѥ����ФǤ�����������������ݾڤ��ޤ���<br>
   This server is Japanese only.
   </font>
</p>

<p>Ver. 1.4.0 ��1 ���åץ��� (2010/02/02 (Tue) 04:25:52) �� <a href="src/">���������</a><br>
��Ver. 1.4.0 ��24 �ΥХ� Fix������ե�����κ����֤ʤ�<br>
</p>

<p>Ver. 1.4.0 ��24 ���åץ��� (2010/01/28 (Thu) 21:29:30) �� <a href="src/">���������</a><br>
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

<p>Ver. 1.4.0 ��23 ���åץ��� (2010/01/10 (Sun) 06:11:24) �� <a href="src/">���������</a><br>
�����Ȥꡦ��ϵ�����դλ����ѹ���󬡦�����μ����ʤ�
</p>

<ol>
  <li>�ƥ����Ѥλ����ѹ���</li>
  <ul>
    <li>�����������ɤΥ��������/���åץ��ɥڡ�����������ޤ���
      (���åץ��ɤϳ�ȯ���������ѤǤ�)</li>
    <li>����������˥��������Υ�󥯤��и����ޤ�
      <font color="#FF0000">(�۵޻��ʳ��ϥ���å����ʤ��ǲ�����)</font></li>
  </ul>

  <li>���߳�ǧ����Ƥ���Х�</li>
  <ul>
    <li>�����ཪλ���ڤ��ؤ�����ȯ���뤬ʣ���Ф뤳�Ȥ����� (SQL ��³���顼�˵����������Ƚ��)</li>
    <li>�����ɼ�����ޤ����ɼ���»������������������ȯ������������롩 (��껪�ǤΤ߳�ǧ)</li>
    <li>ϵ�γ�����ɼ��ʣ����Ǥ����������(��ɼ�Ѥߤˤʤ�ʤ�) (�Ƹ�������)</li>
    <li>����ɼ�η��ɽ������ (DB ����Ͽ��̤������������˵������Ƥ��뤳�Ȥ�Ƚ��)</li>
    <li>��ɼ��̤���ʣ����ɽ������뤳�Ȥ�����</li>
  </ul>

  <li>����Ʒ�</li>
  <ul>
    <li>CSS �� I.E. �б�</li>
    <li>CSS �η����б�</li>
    <li>���� GM ��Ǥ�դ������व������褦�ˤ���</li>
    <li>�ȥ�å��б� (���ߤ� # ��ޤ�ʸ������Ф��ƥ��顼���֤�����)</li>
    <li>���� HTML ���μ�ư��</li>
    <li>�������ȥ⡼�ɻ���GMȯ���б�</li>
    <li>��ư��ɼ��ǽ</li>
    <li>��������������̤β���</li>
    <li>I.E. ����⡼�ɤ�ư���ǧ</li>
    <li>GM �⡼�ɤβ���ڡ����κ���</li>
  </ul>

  <li>���ߺ����� / �����ƥ����Ԥ�����</li>
  <ul>
    <li>�¸��� (��Ŵ�о�פ�𤲤��ü�ʻʺ�)</li>
    <li>ŷ�� (�����˻�˴���ơ�Ŵ�о�פ����褹���ü�ʻʺ�)</li>
    <li>������ (ï����ͤ�ָ��󤻡פˤ��뤵�Ȥ�ΰ���)</li>
    <li>��ì (�Ǥ򼺤ä��Ѥ�������Ψ���⤯�ʤä�ǭ��)</li>
    <li>��� (���٤�������Ψ100%���������Ǥ����Ÿ�)</li>
  </ul>
</ol>
