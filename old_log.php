<?php
require_once('include/init.php');
Loader::LoadFile('oldlog_functions');
Loader::LoadRequest('RequestOldLog');
DB::Connect(RQ::$get->db_no);
if (RQ::$get->is_room) {
  Loader::LoadFile('winner_message', 'icon_class', 'image_class', 'talk_class');

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
  OldLogHTML::Output();
}
else {
  Loader::LoadFile('room_config');
  OldLogHTML::OutputList(RQ::$get->page);
}
HTML::OutputFooter();
