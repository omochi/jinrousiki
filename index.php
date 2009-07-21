<?php require_once('include/setting.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" href="css/index.css">
<title>��Ͽ�ϵ�ʤ�䡩<?php echo $server_comment; ?></title>
</head>
<body>
<?php if($back_page != '') echo "<a href=\"$back_page\">�����</a>"; ?>
<a href="index.php"><img src="img/top_title.jpg"></a>
<div class="comment"><?php echo $server_comment; ?></div>
<noscript>��� JavaScript��ͭ���ˤ��Ƥ������� ���</noscript>
<table class="main">
  <tr><td>
    <div class="menu">��˥塼</div>
    <ul>
      <li><a href="script_info.php">��ħ�Ȼ���</a></li>
      <li><a href="rule.php">������Υ롼��</a></li>
      <li><a href="old_log.php">������</a></li>
      <li><a href="old_log.php?add_role=on">��ɽ��������</a></li>
<!-- ���Ȥ껪��
      <li>��������������</li>
      <li class="log">HTML����</li>
      <li>���Ȥ껪V3��</li>
      <li><a href="log3/index.html">1��378¼</a></li>
      <li><a href="log3_2/index.html">379¼��</a></li>
      <li><a href="log2/index.html">���Ȥ껪V2��</a></li>
      <li><a href="log1/index.html">���Ȥ껪V1��</a></li>
-->
      <li>��������������</li>
      <li><a href="icon_view.php">�����������</a></li>
      <li><a href="icon_upload.php">����������Ͽ</a></li>
      <li>��������������</li>
      <!-- <li><a href="paparazzi.php">�ǥХå��⡼��</a></li> -->
      <li><a href="src/">��ȯ�ǥ��������������</a></li>
      <li>�������� <a href="src/diff.txt">1.4 ��</a>
          <a href="src/diff-1.3.txt">1.3 ��</a><br>(�ƥ����ȥե�����)</li>
      <li><a href="http://sourceforge.jp/projects/jinrousiki/">SourceForge</li>
    </ul>

    <div class="menu">��ή�ѥ�����</div>
    <ul>
      <li><a href="http://jbbs.livedoor.jp/netgame/2829/">���ߥ����ϵ�Ǽ���</a></li>
      <li><a href="http://konoharu.sakura.ne.jp/umigame/yychat/yychat.cgi">���ߥ��Ứ��¼</a></li>
    </ul>

    <div class="menu">�������</div>
    <ul>
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1241277178/l50">�ܥ���</a></li>
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1224519836/l50">ȿ�ʡ������ѥ���</a></li>
      <li><a href="http://www27.atwiki.jp/umigamejinnro/">�������ߥ���wiki</a></li>
      <li><a href="http://jinro.ebb.jp/">�˥������ѻ�</a></li>
      <li><a href="http://jinro.s369.xrea.com/">�˥������ѥƥ��Ȼ�</a></li>
      <li><a href="http://www12.atpages.jp/yaruo/jinro/">ή�з��ﻪ</a></li>
      <li><a href="http://www13.atpages.jp/yaranai/">�鯻��廪</a></li>
      <li>��󥯴�˾�罸��</li>
    </ul>
  </td>

  <td>
    <fieldset>
      <legend>Information <a href="info/index.php">������information�Ϥ������</a></legend>
      <div class="information"><?php include 'info/top.php'; ?></div>
    </fieldset>

    <fieldset>
      <legend>���������</legend>
      <div class="game-list"><?php include 'room_manager.php'; ?></div>
    </fieldset>
    <? OutputSharedServerRoom(); ?>
    <fieldset>
      <legend>¼�κ���</legend><? OutputCreateRoom(); ?>
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
<?php echo 'PHP Ver. ' . PHP_VERSION . ', ' . $script_version . ', LastUpdate: ' . $script_lastupdate; ?>
</div>
</body>
</html>
