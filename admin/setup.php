<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadFile('setup_class');

OutputHTMLHeader(ServerConfig::$title . ServerConfig::$comment . ' [初期設定]');
if (! DB::ConnectInHeader()) SetupDB::CreateDatabase();
echo "</head><body>\n";

SetupDB::CheckTable();
OutputHTMLFooter();
