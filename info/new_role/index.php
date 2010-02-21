<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputFrameHTMLHeader($SERVER_CONF->title . ' [¿·Ìò¿¦¾ğÊó]');
echo <<< EOF
<frameset cols="140, *" border="1" frameborder="1" framespacing="1" bordercolor="#C0C0C0">
<frame name="menu" src="menu.php">
<frame name="body" src="summary.php">

EOF;
OutputFrameHTMLFooter();
