<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadFile('shared_server_config');
$INIT_CONF->LoadRequest('RequestSharedRoom');

if (0 < RQ::$get->id && RQ::$get->id <= count(SharedServerConfig::$server_list)) {
  OutputSharedRoom(RQ::$get->id);
}
else {
  OutputInfoPageHeader('関連サーバ村情報', 0, 'shared_room');
  OutputSharedRoomList();
  HTML::OutputFooter();
}
