<?php
define('JINRO_ROOT', '../..');
require_once(dirname(dirname(dirname(__FILE__))).'/include/init.php');
$INIT_CONF->LoadClass('ROOM_CONF', 'GAME_CONF', 'TIME_CONF', 'ROOM_IMG', 'MESSAGE');
LoadModule(ROOM_CLASSES ,FEEDENGINE_CLASSES);

if(! $dbHandle = ConnectDatabase(true, false)) return false; //DB 接続

$site_summary = FeedEngine::Initialize('site_summary.php');
echo $site_summary->Export();
