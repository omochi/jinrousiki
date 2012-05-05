<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('game_log_class');
$INIT_CONF->LoadRequest('RequestGameLog');
GameLog::Output();
