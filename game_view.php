<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('game_view_class');
$INIT_CONF->LoadRequest('RequestBaseGame', true);
GameView::Output();
