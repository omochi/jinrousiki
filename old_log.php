<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('oldlog_functions');
$INIT_CONF->LoadRequest('RequestOldLog');
DB::Connect(RQ::$get->db_no);
ob_start();
if (RQ::$get->is_room) {
  $INIT_CONF->LoadFile('game_play_functions', 'talk_class');
  $INIT_CONF->LoadClass('ROLES', 'ICON_CONF', 'WINNER_MESS');

  $ROOM = new Room(RQ::$get);
  $ROOM->LoadOption();
  $ROOM->log_mode         = true;
  $ROOM->watch_mode       = RQ::$get->watch;
  $ROOM->single_view_mode = RQ::$get->user_no > 0;
  $ROOM->personal_mode    = RQ::$get->personal_result;
  $ROOM->last_date        = $ROOM->date;

  $USERS = new UserDataSet(RQ::$get);
  $SELF  = $ROOM->single_view_mode ? $USERS->ByID(RQ::$get->user_no) : new User();
  $USERS->player = $ROOM->LoadPlayer();
  if ($ROOM->watch_mode) $SELF->live = 'live';
  if ($ROOM->watch_mode || $ROOM->single_view_mode) $USERS->SaveRoleList();
  OutputOldLog();
}
else {
  $INIT_CONF->LoadClass('ROOM_CONF');
  OutputFinishedRooms(RQ::$get->page);
}
OutputHTMLFooter();
ob_end_flush();
