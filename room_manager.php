<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('room_manager_class');

if (! DB::ConnectInHeader()) return false;
RoomManager::Maintenance();
Text::EncodePostData();
if (@$_POST['command'] == 'CREATE_ROOM') {
  $INIT_CONF->LoadFile('message', 'request_class', 'user_icon_class', 'twitter_class');
  //$INIT_CONF->LoadFile('feedengine');
  RoomManager::Create();
}
else {
  $INIT_CONF->LoadFile('chaos_config');
  RoomManager::OutputList();
}
DB::Disconnect();
