<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('GAME_OPT_CAPT');
OutputHTMLHeader($SERVER_CONF->title . '[ゲームオプション]', 'info');
?>
</head>
<body>
<h1>ゲームオプション</h1>
<p>
<a href="../" target="_top">&lt;= TOP</a>
<a href="./" target="_top">←情報一覧</a>
</p>
<h2><?= $GAME_OPT_MESS->quiz ?></h2>
<pre>
作成中・・・
</pre>
</body></html>
