<p>
<h1>���Υ����ФˤĤ���</h1>
<font color="#FF0000">
�����ϡ�<a href="http://sourceforge.jp/projects/jinrousiki/">��ϵ�� ����Ͽ�ϵ�ʤ�䡩 ��������Ʊ������</a>�׸����ƥ������ѥ����ФǤ���<br>
��������������ݾڤ��ޤ���<br>
This server is Japanese only. -&gt; <a href="http://sourceforge.jp/projects/jinrousiki/">SourceForge</a>
</font>
</p>

<h1>TOPIC</h1>

<h2>Ver. 1.4.0 ��9 ���åץ��� (2010/05/29 (Sat) 05:29:15) �� <a href="src/">���������</a></h2>
<ul>
  <li>Twitter ��Ƶ�ǽ�μ���</li>
  <li>BBS ɽ����ǽ�μ���</li>
  <li>��Ʈ¼�������ѹ������ƥ�μ���</li>
  <li>�ּ��Լԡס�ȿ���աסֿ��Ż��ԡסֲ�Ƹ�ס�ŷϵ�ס�������סָ�����<br>
    ����ѡס��������סִ�ѻաס�Ǯ�¡סּ����ȡ׼���
  </li>
  <li>��̴��͡ס�̴�Ǽԡס��ӡס�ŷ�ѡס������ϡפλ����ѹ�</li>
</ul>

<h1>��ȯ����</h1>
<pre>
% ����BOT�۵��б������� %
room_manager.php % 45���ܡ�
  //���ϥǡ����Υ��顼�����å�
  $room_name    = $_POST['room_name'];
  $room_comment = $_POST['room_comment'];
  EscapeStrings($room_name);
  EscapeStrings($room_comment);
  if($room_name == '' || $room_comment == ''){ //̤���ϥ����å�
    OutputRoomAction('empty');
    return false;
  }

  //ʸ��������å�
  $ng_word = '/http:\/\//i';
  if(preg_match($ng_word, $room_name) ||
     preg_match($ng_word, $room_comment)){
    OutputActionResult('¼���� [���ϥ��顼]', '̵����¼̾�������Ǥ���');
  }
</pre>

<h2>�������� / �����ѹ�</h2>
<ul>
  <li>�ʤ�</li>
</ul>

<h2>���ߺ����� / �����ƥ����Ԥ�</h2>
<ul>
  <li>�ͷ����� / �峤�ͷ���¾�ι�Ǹ�����² / ����</li>
  <li>�������ɼ�������˻�������ղä���Ż���</li>
  <li>�����𡧰���������˻�˴����</li>
</ul>
