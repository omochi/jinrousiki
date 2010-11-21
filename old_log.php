<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('oldlog_functions');
$INIT_CONF->LoadRequest('RequestOldLog'); //引数を取得
$DB_CONF->ChangeName($RQ_ARGS->db_no); //DB 名をセット
$DB_CONF->Connect(); //DB 接続
if($RQ_ARGS->is_room){
  $INIT_CONF->LoadFile('game_play_functions', 'user_class', 'talk_class');
  $INIT_CONF->LoadClass('ROLES', 'ICON_CONF', 'VICT_MESS');

  $ROOM =& new Room($RQ_ARGS);
  $ROOM->log_mode = true;
  $ROOM->last_date = $ROOM->date;

  $USERS =& new UserDataSet($RQ_ARGS);
  $SELF = $RQ_ARGS->user_no > 0 ? $USERS->ByID($RQ_ARGS->user_no) : new User();
  if($RQ_ARGS->user_no > 0 || $RQ_ARGS->watch) $SELF->live = 'live';
  OutputOldLog();
}
else{
  $INIT_CONF->LoadClass('ROOM_CONF', 'CAST_CONF', 'ROOM_IMG', 'GAME_OPT_MESS');
  OutputFinishedRooms($RQ_ARGS->page);
}
OutputHTMLFooter();
