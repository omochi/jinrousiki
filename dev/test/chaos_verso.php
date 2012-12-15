<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
Loader::LoadFile('room_config', 'chaos_config', 'role_data_class', 'cast_class',
		 'game_vote_functions', 'test_class');
ChaosVersoTest::Output();
