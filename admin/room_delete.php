<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');

if (! ServerConfig::$debug_mode) {
  HTML::OutputResult('認証エラー', 'このスクリプトは使用できない設定になっています。');
}

extract($_GET, EXTR_PREFIX_ALL, 'unsafe');
$room_no = intval($unsafe_room_no);
if ($room_no < 1) HTML::OutputResult('部屋削除[エラー]', '無効な村番号です。');

DB::Connect();
if (DB::Lock('room') && DB::DeleteRoom($room_no)) {
  DB::Optimize();
  HTML::OutputResult('部屋削除', $room_no . ' 番地を削除しました。トップページに戻ります。', '../');
}
else {
  HTML::OutputResult('部屋削除[エラー]', $room_no . ' 番地の削除に失敗しました。');
}
