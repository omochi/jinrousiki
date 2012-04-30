<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadFile('test/objection');
ObjectionTest::Output();
