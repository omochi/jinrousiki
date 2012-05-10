<?php
require_once('include/init.php');
$INIT_CONF->LoadFile('oldlog_functions');
$INIT_CONF->LoadRequest('RequestOldLog');
DB::Connect(RQ::$get->db_no);
ob_start();
if (RQ::$get->is_room) {
  $INIT_CONF->LoadFile('icon_class', 'talk_class', 'game_play_functions');
  $INIT_CONF->LoadClass('ROLES', 'WINNER_MESS');

  DB::$ROOM = new Room(RQ::$get);
  DB::$ROOM->LoadOption();
  DB::$ROOM->log_mode         = true;
  DB::$ROOM->watch_mode       = RQ::$get->watch;
  DB::$ROOM->single_view_mode = RQ::$get->user_no > 0;
  DB::$ROOM->personal_mode    = RQ::$get->personal_result;
  DB::$ROOM->last_date        = DB::$ROOM->date;

  DB::$USER = new UserDataSet(RQ::$get);
  DB::$SELF = DB::$ROOM->single_view_mode ? DB::$USER->ByID(RQ::$get->user_no) : new User();
  DB::$USER->player = DB::$ROOM->LoadPlayer();
  if (DB::$ROOM->watch_mode) DB::$SELF->live = 'live';
  if (DB::$ROOM->watch_mode || DB::$ROOM->single_view_mode) DB::$USER->SaveRoleList();
  OutputOldLog();
}
else {
  $INIT_CONF->LoadFile('room_config');
  OutputFinishedRooms(RQ::$get->page);
}
HTML::OutputFooter();
ob_end_flush();
