<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('login_class');
$INIT_CONF->LoadRequest('RequestLogin', true);
Login::Execute();
