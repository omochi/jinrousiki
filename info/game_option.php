<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('GAME_OPT_CAPT');
OutputHTMLHeader($SERVER_CONF->title . '[�����४�ץ����]', 'info');
?>
</head>
<body>
<h1>�����४�ץ����</h1>
<p>
<a href="../" target="_top">&lt;= TOP</a>
<a href="./" target="_top">���������</a>
</p>
<h2><?= $GAME_OPT_MESS->quiz ?></h2>
<pre>
�����桦����
</pre>
</body></html>
