<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');

$disable = true; //使用時には false に変更する
if ($disable) {
  HTML::OutputResult('認証エラー', 'このスクリプトは使用できない設定になっています。');
}

$INIT_CONF->LoadFile('room_config', 'cast_config', 'oldlog_functions');
$INIT_CONF->LoadClass('ROOM_IMG', 'GAME_OPT_MESS');
$INIT_CONF->LoadRequest('RequestOldLog'); //引数を取得
DB::Connect(RQ::$get->db_no);

RQ::$get->generate_index = true;
RQ::$get->index_no = 8; //インデックスページの開始番号
RQ::$get->min_room_no = 351; //インデックス化する村の開始番号
RQ::$get->max_room_no = 383; //インデックス化する村の終了番号
RQ::$get->prefix = ''; //各ページの先頭につける文字列 (テスト / 上書き回避用)
RQ::$get->add_role = true;
RQ::$get->heaven_talk = true;

$db_delete_mode = false; //部屋削除のみ
if ($db_delete_mode) {
  HTML::OutputHeader('DB削除モード', null, true);
  for ($i = RQ::$get->min_room_no; $i <= RQ::$get->max_room_no; $i++) {
    DB::DeleteRoom($i);
    printf('%d 番地を削除しました<br>', $i);
  }
  DB::Optimize();
  HTML::OutputFooter(true);
}

//GenerateLogIndex(); //インデックスページ生成
//HTML::OutputFooter(true);

$INIT_CONF->LoadFile('icon_class', 'talk_class', 'game_play_functions');
$INIT_CONF->LoadClass('WINNER_MESS', 'ROLES');

$room_delete = false; //DB削除設定
$header = sprintf('../log_test/%s', RQ::$get->prefix);
$footer = '</body></html>'."\n";
for ($i = RQ::$get->min_room_no; $i <= RQ::$get->max_room_no; $i++) {
  RQ::$get->room_no = $i;
  DB::$ROOM = new Room(RQ::$get);
  DB::$ROOM->log_mode = true;
  DB::$ROOM->last_date = DB::$ROOM->date;

  DB::$USER = new UserDataSet(RQ::$get);
  DB::$SELF = new User();

  RQ::$get->reverse_log = false;
  file_put_contents("{$header}{$i}.html", GenerateOldLog() . $footer);

  RQ::$get->reverse_log = true;
  DB::$ROOM = new Room(RQ::$get);
  DB::$ROOM->log_mode = true;
  DB::$ROOM->last_date = DB::$ROOM->date;

  DB::$USER = new UserDataSet(RQ::$get);
  DB::$SELF = new User();
  file_put_contents("{$header}{$i}r.html", GenerateOldLog() . $footer);
  if ($room_delete) DB::DeleteRoom($i);
}
if ($room_delete) DB::Optimize();

HTML::OutputResult('ログ生成',
		   RQ::$get->min_room_no . ' 番地から ' .
		   RQ::$get->max_room_no . ' 番地までを HTML 化しました');
