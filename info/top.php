<p>
  <font color="#FF0000">�����ϥƥ������ѥ����ФǤ���</font>
</p>

<p>
<a href="http://satori.crz.jp/">�����Ȥ껪</a> (Ver. 1.3.0) �Ȥμ�ʰ㤤��
<a href="info/history.php">��������</a> �򻲾Ȥ��Ƥ���������<br>
<ol>
  <li>�ƥ����Ѥλ����ѹ���</li>
  <ul>
    <li>�����������ɤΥ��������/���åץ��ɥڡ�����������ޤ���
      (���åץ��ɤϳ�ȯ���������ѤǤ�)</li>
    <li>����������˥��������Υ�󥯤��и����ޤ�
      <font color="#FF0000">(�۵޻��ʳ��ϥ���å����ʤ��ǲ�����)</font></li>
  </ul>

  <li>Ver. 1.4.0��19 �����ǥХå����� (2009/09/03 03:30 �ɲ�)</li>
  <ul>
    <li>include/game_functions.php % 1112���� (2009/09/03 03:30 �ɲ�)<br>
      �� global $GAME_CONF, $room_no, $ROOM<font color="#FF0000">, $vote_times</font>;<br>
      �� global $GAME_CONF, $room_no, $ROOM;<br>
    </li>
    <li>include/game_functions.php % 1153���� (2009/09/03 03:30 �ɲ�)<br>
      �� elseif($check_draw && <font color="#FF0000">$vote_times</font> >= $GAME_CONF->draw) //����ʬ��<br>
      �� elseif($check_draw && <font color="#FF0000">GetVoteTimes()</font> >= $GAME_CONF->draw) //����ʬ��<br>
    </li>
    <li>include/config.php % 251����<br>
      �� var $<font color="#FF0000">week</font>_replace_list = array( ...<br>
      �� var $<font color="#FF0000">weekly</font>_replace_list = array( ...<br>
    </li>
    <li>include/game_functions.php % 459����<br>
      �� &lt;param name=&quot;movie&quot; value=&quot;{$SOUND-><font color="#FF0000">type</font>}&quot;&gt;<br>
      �� &lt;param name=&quot;movie&quot; value=&quot;{$SOUND-><font color="#FF0000">$type</font>}&quot;&gt;<br>
    </li>
    <li>include/game_functions.php % 461����<br>
      �� &lt;embed src=&quot;{$SOUND-><font color="#FF0000">type</font>}&quot; type= ...<br>
      �� &lt;embed src=&quot;{$SOUND-><font color="#FF0000">$type</font>}&quot; type= ...<br>
    </li>
    <li>game_vote.php % 679����<br>
      �� $this_voted_number .&quot;\t&quot; . $this_vote_number . &quot;\t&quot; . $RQ_ARGS->vote_times;<br>
      �� <font color="#FF0000">(int)</font>$this_voted_number .&quot;\t&quot; . <font color="#FF0000">(int)</font>$this_vote_number . &quot;\t&quot; . $RQ_ARGS->vote_times;<br>
    </li>
    <li>game_vote.php % 1423����<br>
      �� $this_new_role = str_replace('mania', $this_result, $this_<font color="#FF0000">target</font>->role) . ' copied';<br>
      �� $this_new_role = str_replace('mania', $this_result, $this_<font color="#FF0000">user</font>->role) . ' copied';<br>
    </li>
  </ul>

  <li>���߳�ǧ����Ƥ���Х�</li>
  <ul>
    <li>�����ཪλ���ڤ��ؤ�����ȯ���뤬ʣ���Ф뤳�Ȥ����� (SQL ��³���顼�˵����������Ƚ��)</li>
    <li>�����ɼ�����ޤ����ɼ���»������������������ȯ������������롩 (��껪�ǤΤ߳�ǧ)</li>
    <li>ϵ�γ�����ɼ��ʣ����Ǥ����������(��ɼ�Ѥߤˤʤ�ʤ�) (�Ƹ�������)</li>
    <li>����ɼ�η��ɽ������ (DB ����Ͽ��̤������������˵������Ƥ��뤳�Ȥ�Ƚ��)</li>
    <li>�ְ۵Ĥ���פβ�������ɤ��뤿�Ӥ��Ĥ� (Ĵ����Τ��ḽ�߲�����ߤ�Ƥ��ޤ�)</li>
  </ul>

  <li>����Ʒ�</li>
  <ul>
    <li>CSS �� I.E. �б�</li>
    <li>CSS �η����б�</li>
    <li>�ǥХå��ѥ�ɽ����ǽ�μ���</li>
    <li>���顼��å������β���</li>
    <li>���� GM ��Ǥ�դ������व������褦�ˤ���</li>
    <li>������¼���ѽ����μ��� �� Ver. 1.4 �ϤǼ����ʹ���</li>
    <li>�ȥ�å��б� (���ߤ� # ��ޤ�ʸ������Ф��ƥ��顼���֤�����)</li>
    <li>mysql_query() �Υ�åѴؿ����� (���顼�к�)</li>
    <li>���� HTML ���μ�ư��</li>
  </ul>
</ol>
</p>
