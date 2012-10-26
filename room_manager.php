<?php
require_once('include/init.php');
Loader::LoadFile('room_manager_class');

if (! DB::ConnectInHeader()) return false;
if (Loader::IsLoaded('index_functions')) RoomManager::Maintenance();
Text::Encode();
if (@$_POST['command'] == 'CREATE_ROOM') {
  Loader::LoadFile('message', 'request_class', 'user_icon_class', 'twitter_class');
  //Loader::LoadFile('feedengine');
  RoomManager::Create();
}
else {
  Loader::LoadFile('chaos_config');
  RoomManager::OutputList();
}
DB::Disconnect();
