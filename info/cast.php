<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadFile('info_functions');
$INIT_CONF->LoadClass('ROLE_DATA');
OutputInfoPageHeader('配役一覧', 0, 'cast');
OutputCastTable();
HTML::OutputFooter();
