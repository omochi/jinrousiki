<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('GAME_CONF', 'CAST_CONF');
OutputInfoPageHeader('�ռ����Ǻ�', 0, 'info_cast');
$config = new CopyrightConfig();
$config->Output();
OutputHTMLFooter();
