<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('SCRIPT_INFO', 'MENU_LINK', 'BBS_CONF');
OutputHTMLHeader($SERVER_CONF->title . $SERVER_CONF->comment, 'index');
echo "</head>\n<body>\n";
if($SERVER_CONF->back_page != ''){
  echo '<a href="' . $SERVER_CONF->back_page . '">�����</a><br>'."\n";
}
?>
<a href="./"><img src="img/top_title.jpg"></a>
<div class="comment"><?php echo $SERVER_CONF->comment ?></div>
<noscript>&lt;&lt; JavaScript��ͭ���ˤ��Ƥ������� &gt;&gt;</noscript>
<table class="main">
  <tr><td>
    <div class="menu">��˥塼</div>
    <ul>
      <li><a href="script_info.php">��ħ�Ȼ���</a></li>
      <li><a href="rule.php">������Υ롼��</a></li>
      <li><a href="info/chaos.php">����⡼��</a></li>
      <li><a href="info/new_role/">���򿦾���</a></li>
      <li><a href="info/">����¾�ξ������</a></li>
      <li>��������������</li>
      <li><a href="old_log.php">������</a></li>
      <li>��������������</li>
      <li><a href="icon_view.php">�����������</a></li>
      <li><a href="icon_upload.php">����������Ͽ</a></li>
      <li>��������������</li>
      <li><a href="src/">�����������ɥ��������</a></li>
    </ul>
    <?php $MENU_LINK->Output() ?>
  </td>

  <td>
    <fieldset>
      <legend>Information <a href="info/history/top.php">������information�Ϥ������</a></legend>
      <div class="information"><?php include_once 'info/top.php' ?></div>
    </fieldset>

    <fieldset>
      <legend>���������</legend>
      <div class="game-list"><?php include_once 'room_manager.php' ?></div>
    </fieldset>
    <?php $BBS_CONF->Output(); OutputSharedServerRoom() ?>
    <fieldset>
      <legend>¼�κ���</legend><?php OutputCreateRoomPage() ?>
    </fieldset>
  </td></tr>
</table>

<div class="footer"><?php $SCRIPT_INFO->OutputVersion() ?></div>
</body></html>
