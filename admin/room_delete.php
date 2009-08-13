<?php
require_once(dirname(__FILE__) . '/../include/functions.php');

$CSS_PATH = '../css'; //CSS のパス設定

if(! $DEBUG_MODE)
  OutputActionResult('認証エラー', 'このスクリプトは使用できない設定になっています。');

extract($_GET, EXTR_PREFIX_ALL, 'unsafe');
$room_no = intval($unsafe_room_no);
if($room_no < 1) OutputActionResult('部屋削除[エラー]', '無効な村番号です。');

$dbHandle = ConnectDatabase(); //DB 接続
mysql_query(sprintf("DELETE FROM talk WHERE room_no=%d", $room_no));
mysql_query(sprintf("DELETE FROM system_message WHERE room_no=%d", $room_no));
mysql_query(sprintf("DELETE FROM vote WHERE room_no=%d", $room_no));
mysql_query(sprintf("DELETE FROM user_entry WHERE room_no=%d", $room_no));
mysql_query(sprintf("DELETE FROM room WHERE room_no=%d", $room_no));
mysql_query("OPTIMIZE TABLE admin_manage, room, system_message , talk, user_entry, vote");
OutputActionResult('部屋削除',
		   $room_no . ' 番地を削除しました。トップページに戻ります。',
		   '../index.php');
?>
