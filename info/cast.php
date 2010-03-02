<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('GAME_CONF', 'CAST_CONF');
OutputInfoPageHeader('ÇÛÌò°ìÍ÷', 0, 'info_cast');
OutputCastTable();
OutputHTMLFooter();
