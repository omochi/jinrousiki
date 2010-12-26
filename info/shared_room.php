<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('SHARED_CONF');
OutputInfoPageHeader('関連サーバ村情報', 0, 'shared_room');
$SHARED_CONF->Output();
OutputHTMLFooter();
