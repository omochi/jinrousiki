<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('SHARED_CONF');
$INIT_CONF->LoadRequest('RequestSharedRoom');

if (0 < RQ::$get->id && RQ::$get->id <= count($SHARED_CONF->server_list)) {
  OutputSharedRoom(RQ::$get->id);
}
else {
  OutputInfoPageHeader('関連サーバ村情報', 0, 'shared_room');
  OutputSharedRoomList();
  OutputHTMLFooter();
}
