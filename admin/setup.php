<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadFile('setup_class');

HTML::OutputHeader(ServerConfig::$title . ServerConfig::$comment . ' [初期設定]', null, true);
if (! DB::ConnectInHeader()) SetupDB::CreateDatabase();
SetupDB::CheckTable();
HTML::OutputFooter();
