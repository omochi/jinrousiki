<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('CAST_CONF', 'ROLE_DATA');
OutputInfoPageHeader('ÇÛÌò°ìÍ÷', 0, 'info_cast');
OutputCastTable();
OutputHTMLFooter();
