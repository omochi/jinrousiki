<?php
//error_reporting(E_ALL);
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
Loader::LoadFile('test_class', 'chaos_config', 'role_data_class', 'cast_class', 'room_option_class',
		 'game_vote_functions');
RoleTest::Output();
