<?php
require_once('include/init.php');
Loader::LoadFile('user_manager_class');
Loader::LoadRequest('RequestUserManager');
DB::Connect();
Session::Start();
RQ::Get()->entry ? UserManager::Entry() : UserManager::Output();
DB::Disconnect();
