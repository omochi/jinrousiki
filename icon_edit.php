<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('icon_edit_class');
$INIT_CONF->LoadRequest('RequestIconEdit');
IconEdit::Execute();
