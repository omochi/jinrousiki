<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('icon_functions');
$INIT_CONF->LoadRequest('RequestIconView'); //���������
$DB_CONF->Connect(); //DB ��³
OutputIconPageHeader();
OutputIconList();
OutputIconPageFooter();
