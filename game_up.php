<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('game_up_class');
$INIT_CONF->LoadRequest('RequestGameUp', true);
GameUp::Output();
