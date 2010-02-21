<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('SCRIPT_INFO');
OutputHTMLHeader($SERVER_CONF->title . $SERVER_CONF->comment, 'index');
echo "</head>\n<body>\n";
if($SERVER_CONF->back_page != ''){
  echo '<a href="' . $SERVER_CONF->back_page . '">�����</a><br>'."\n";
}
?>
<a href="./"><img src="img/top_title.jpg"></a>
<div class="comment"><?= $SERVER_CONF->comment ?></div>
<noscript>��� JavaScript��ͭ���ˤ��Ƥ������� ���</noscript>
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
      <li><a href="old_log.php">������</a> (<a href="old_log.php?add_role=on">+ ��ɽ��</a>)</li>
<!-- ���Ȥ껪��
      <li class="log">HTML����</li>
      <li>���Ȥ껪V3��</li>
      <li><a href="log3/">1��378¼</a></li>
      <li><a href="log3_2/">379¼��</a></li>
      <li><a href="log2/">���Ȥ껪V2��</a></li>
      <li><a href="log1/">���Ȥ껪V1��</a></li>
-->
      <li>��������������</li>
      <li><a href="icon_view.php">�����������</a></li>
      <li><a href="icon_upload.php">����������Ͽ</a></li>
      <li>��������������</li>
      <li><a href="src/">�����������ɥ��������</a></li>
      <li><a href="http://sourceforge.jp/projects/jinrousiki/">SourceForge</a></li>
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1240771280/l50">��ȯ���Х���𥹥�</a></li>
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1246414115/l50">������ƥ���</a></li>
    </ul>

    <div class="menu">��ή�ѥ�����</div>
    <ul>
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1260623018/l50">�ܥ���</a>(���ΤϤ���)</li>
      <li><a href="http://www27.atwiki.jp/umigamejinnro/">�������ߥ���wiki</a></li>
      <li><a href="http://jbbs.livedoor.jp/netgame/2829/">���ߥ����ϵ�Ǽ���</a></li>
      <li><a href="http://umigamejinrou.chatx2.whocares.jp/">���ߥ��Ứ��¼</a></li>
      <!-- <li><a href="http://konoharu.sakura.ne.jp/umigame/yychat/yychat.cgi">�쥦�ߥ��Ứ��¼</a></li> -->
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1224519836/l50">ȿ�ʡ������ѥ���</a></li>
    </ul>

    <div class="menu">�������</div>
    <ul>
      <li>�������ߥ����</li>
      <li><a href="http://alicegame.dip.jp/sanae/">���Ļ�</a></li>
      <!-- <li><a href="http://satori.crz.jp/">���Ȥ껪</a></li> -->
      <li><a href="http://www7.atpages.jp/izayoi398/">���뻪</a></li>
      <li><a href="http://www12.atpages.jp/cirno/">����λ��ʳ�ȯ�������</a></li>
      <li>����׷�</li>
      <li><a href="http://www16.atpages.jp/sasugabros/">ή����Ի�</a></li>
      <li><a href="http://alicegame.dip.jp/suisei/">�����л�</a></li>
      <li><a href="http://www37.atwiki.jp/yaruomura/">�����wiki</a></li>
      <li>����׷�ͽ��/�ݴɸ˻�</li>
      <li><a href="http://www12.atpages.jp/yaruo/jinro/">ή�з��ﻪ</a></li>
      <li><a href="http://alicegame.dip.jp/sousei/">�����Хƥ��Ȼ�</a></li>
      <li><a href="http://www13.atpages.jp/yaranai/">�鯻��廪</a></li>
      <li><a href="http://www13.atpages.jp/suigintou/">��仪</a></li>
      <li><a href="http://www15.atpages.jp/kanaria/">�����ݴɸ�</a></li>
      <li><a href="http://www14.atpages.jp/mmr1/">��������</a></li>
      <!-- <li><a href="http://www15.atpages.jp/seikima2/jinro_php/">���������ʥƥ��Ȼ���</a></li> -->
      <li>��������Ŵ��</li>
      <li><a href="http://bourbonhouse.xsrv.jp/jinro/">�С��ܥ�ϥ�����</a></li>
      <li><a href="http://dynamis.xsrv.jp/jinro/">΢������</a></li>
      <li><a href="http://www16.atpages.jp/bourbonjinro/">��С��ܥ�ϥ�����</a></li>
      <li>iM@S��</li>
      <li><a href="http://kiterew.tv/jinro/">��Ļ��</a></li>
      <li>�С��ܥ󻪷�</li>
      <li><a href="http://jinro.blue-sky-server.com/">ǭ����</a></li>
      <li><a href="http://www.freedom.xii.jp/jinro/">�С��ܥ�</a></li>
      <li>��󥯴�˾�罸��</li>
    </ul>
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
    <?php OutputSharedServerRoom() ?>
    <fieldset>
      <legend>¼�κ���</legend><?php OutputCreateRoomPage() ?>
    </fieldset>
  </td></tr>
</table>

<div class="footer">
[PHP4 + MYSQL������ץȡ�<a href="http://p45.aaacafe.ne.jp/~netfilms/" target="_blank">���ۥۡ���ڡ���</a>]
[�����ƥࡡ<a href="http://sourceforge.jp/projects/mbemulator/" target="_blank">mbstring���ߥ�졼��</a>]<br>
[�̿��Ǻࡡ<a href="http://keppen.web.infoseek.co.jp/" target="_blank">ŷ�η���</a>
��<a href="http://moineau.fc2web.com/" target="_blank">Le moineau - ������Τ���� -</a>
<!-- ����λ��Τߤǻ���
��<a href="http://moineau.fc2web.com/" target="_blank">Le moineau - ������Τ���� -</a>
-->
]
[�ե�����Ǻࡡ<a href="http://azukifont.mints.ne.jp/" target="_blank">�������ե����</a>]<br>
[���������Ǻࡡ
 <a href="http://natuhotaru.yukihotaru.com/" target="_blank">�Ʒ�</a>
 <a href="http://jigizagi.s57.xrea.com/" target="_blank">���������Τ����Ϥ�</a>
]<br>
<!-- ����λ��ǻ���
[���������Ǻࡡ
 <a href="http://natuhotaru.yukihotaru.com/" target="_blank">�Ʒ�</a>
 <a href="http://jigizagi.s57.xrea.com/" target="_blank">���������Τ����Ϥ�</a>
]<br>
-->
<!-- ���Ȥ껪��
[���������Ǻࡡ
 <a href="http://natuhotaru.yukihotaru.com/" target="_blank">�Ʒ�</a>
 <a href="http://jigizagi.s57.xrea.com/" target="_blank">���������Τ����Ϥ�</a>
 <a href="http://www.geocities.jp/nwqkp334/" target="_blank">��������</a>
 <a href="http://www21.tok2.com/home/foxy/" target="_blank">Foxy���ѿ���</a>
 <a href="http://kukyo.hp.infoseek.co.jp/" target="_blank">�֤�󤱤ä�</a>
 <a href="http://www8.plala.or.jp/denpa/indexdon.html" target="_blank">����Ч</a>
]<br>
-->
<?php $SCRIPT_INFO->OutputVersion() ?>
</div>
</body></html>
