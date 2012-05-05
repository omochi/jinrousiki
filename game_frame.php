<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('game_frame_class');
$INIT_CONF->LoadRequest('RequestGameFrame', true);
GameFrame::Output();
