<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('icon_view_class');
$INIT_CONF->LoadRequest('RequestIconView');
IconView::Output();