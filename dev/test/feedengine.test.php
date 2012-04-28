<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('ROOM_IMG', 'MESSAGE');
$INIT_CONF->LoadFile('game_config', 'room_config', 'time_config', 'feedengine');

DB::Connect(); // DB 接続
$site_summary = FeedEngine::Initialize('site_summary.php');
echo $site_summary->Export();
