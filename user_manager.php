<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('user_manager_class');
$INIT_CONF->LoadRequest('RequestUserManager');
DB::Connect();
RQ::$get->entry ? UserManager::Entry() : UserManager::Output();
DB::Disconnect();
