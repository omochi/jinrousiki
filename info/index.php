<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
OutputFrameHTMLHeader($SERVER_CONF->title . ' [情報一覧]');
echo <<< EOF
<frameset cols="180, *" border="1" frameborder="1" framespacing="1" bordercolor="#C0C0C0">
<frame name="menu" src="menu.php">
<frame name="body" src="history">

EOF;
OutputFrameHTMLFooter();
