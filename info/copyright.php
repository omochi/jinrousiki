<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('COPYRIGHT');
OutputInfoPageHeader('謝辞・素材', 0, 'info_cast');
$COPYRIGHT->Output();
OutputHTMLFooter();
